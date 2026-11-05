<?php

declare(strict_types=1);

namespace Tests\Feature\Recurring;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Expense;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

/**
 * Covers Expense wired to the Liberu\Accounting\Erp\Concerns\Recurring engine: a single-row
 * template (no line items, no number column) catches up one draft per missed
 * occurrence, honours the per-run safety cap, and carries team_id to children.
 */
class RecurringExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time on a mid-month day: addMonth() overflows on month-end
        // dates (Jan 31 -> Mar 3), which would make occurrence counts flaky.
        Carbon::setTestNow('2026-06-15');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * expenses.user_id and expenses.team_id are both FK-enforced, so each
     * template needs a real owner + team; both default here unless overridden.
     */
    private function template(array $overrides = []): Expense
    {
        $user = User::factory()->create();
        $team = Team::forceCreate([
            'user_id' => $user->id,
            'name' => 'Ops',
            'personal_team' => true,
        ]);

        return Expense::create(array_merge([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'amount' => 100.00,
            'description' => 'Monthly SaaS',
            'date' => Carbon::now()->toDateString(),
            'is_recurring' => true,
            'recurrence_frequency' => 'monthly',
            'recurrence_start' => Carbon::now()->toDateString(),
            'last_generated' => null,
        ], $overrides));
    }

    public function test_catch_up_generates_one_draft_per_missed_occurrence(): void
    {
        $start = Carbon::now()->subMonthsNoOverflow(3);

        $template = $this->template([
            'recurrence_start' => $start->toDateString(),
            'last_generated' => null,
        ]);

        // The engine is start-inclusive: with last_generated=null it emits an
        // occurrence AT recurrence_start, then every step <= today. A template
        // 3 months back therefore yields 4 drafts (months 0,1,2,3=today), not 3.
        $expected = [
            $start->copy(),
            $start->copy()->addMonth(),
            $start->copy()->addMonths(2),
            $start->copy()->addMonths(3), // == today (2026-06-15)
        ];

        $created = $template->generateDue();

        $this->assertSame(count($expected), $created);

        $children = Expense::where('is_recurring', false)->orderBy('date')->get();
        $this->assertCount(count($expected), $children);

        foreach ($children as $i => $child) {
            $this->assertSame($expected[$i]->toDateString(), $child->date->toDateString(), "child #{$i} date");
            $this->assertSame('pending', $child->approval_status);
            $this->assertFalse((bool) $child->is_recurring);
        }

        // No line-item relation exists on Expense; generation must not error.
        $this->assertSame(
            end($expected)->toDateString(),
            $template->fresh()->last_generated->toDateString()
        );

        // Idempotent: an immediate re-run creates nothing more.
        $this->assertSame(0, $template->generateDue());
        $this->assertSame(count($expected), Expense::where('is_recurring', false)->count());
    }

    public function test_safety_cap_bounds_a_run_and_resumes_on_the_next(): void
    {
        $start = Carbon::now()->subYears(5); // ~1826 daily occurrences due

        $template = $this->template([
            'recurrence_frequency' => 'daily',
            'recurrence_start' => $start->toDateString(),
            'last_generated' => null,
        ]);

        // First run stops at the cap instead of running away.
        $this->assertSame(120, $template->generateDue());

        // 120 occurrences = days 0..119 => last_generated advanced by 119 days.
        $this->assertSame(
            $start->copy()->addDays(119)->toDateString(),
            $template->fresh()->last_generated->toDateString()
        );

        // Cap is per-run: the next call resumes where it left off (no gap, no
        // lost occurrence) and generates the next 120.
        $this->assertSame(120, $template->generateDue());
        $this->assertSame(240, Expense::where('is_recurring', false)->count());
    }

    public function test_generated_children_inherit_team_id(): void
    {
        $owner = User::factory()->create();
        // forceCreate: id/user_id aren't mass-assignable on Team (id must never be).
        Team::forceCreate(['id' => 7, 'user_id' => $owner->id, 'name' => 'Acme', 'personal_team' => false]);

        $template = $this->template([
            'team_id' => 7,
            'recurrence_start' => Carbon::now()->subMonthsNoOverflow(2)->toDateString(),
            'last_generated' => null,
        ]);

        $this->assertSame(3, $template->generateDue());

        $children = Expense::where('is_recurring', false)->get();
        $this->assertCount(3, $children);

        foreach ($children as $child) {
            $this->assertSame(7, (int) $child->team_id);
        }
    }
}
