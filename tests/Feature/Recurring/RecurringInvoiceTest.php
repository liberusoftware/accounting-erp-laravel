<?php

declare(strict_types=1);

namespace Tests\Feature\Recurring;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\InvoiceItem;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Team;
use Tests\TestCase;

class RecurringInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze on a day-of-month <= 28 so subMonthsNoOverflow(3) and the
        // trait's monthly addMonth stepping are exact inverses; otherwise the
        // catch-up count is date-dependent (short-month overflow), which would
        // make assertSame() flaky day to day.
        Carbon::setTestNow('2026-06-15');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeTemplate(array $overrides = []): Invoice
    {
        $tpl = Invoice::factory()->create(array_merge([
            'is_recurring' => true,
            'recurrence_frequency' => 'monthly',
            'recurrence_start' => today()->subMonthsNoOverflow(3), // 2026-03-15
            'recurrence_end' => null,
            'last_generated' => null,
        ], $overrides));

        foreach (['line one', 'line two'] as $description) {
            InvoiceItem::create([
                'invoice_id' => $tpl->id,
                'description' => $description,
                'quantity' => 1,
                'unit_price' => 100,
                'amount' => 100,
                'tax_amount' => 0,
            ]);
        }

        return $tpl->refresh();
    }

    private function children(Invoice $template): Collection
    {
        return Invoice::where('id', '!=', $template->id)
            ->orderBy('invoice_date')
            ->get();
    }

    public function test_catch_up_generates_one_draft_per_missed_occurrence(): void
    {
        $tpl = $this->makeTemplate();

        // start = 2026-03-15, monthly → occurrences 03-15, 04-15, 05-15,
        // 06-15 (== today). All <= today, so four drafts are due.
        $this->assertSame(4, $tpl->generateDue());

        $children = $this->children($tpl);
        $this->assertCount(4, $children);

        $numbers = [];
        $previousDate = null;
        foreach ($children as $child) {
            $this->assertFalse($child->is_recurring);                 // a draft, not a template
            $this->assertSame('pending', $child->payment_status);
            $this->assertCount(2, $child->items);                     // both line items cloned
            $this->assertNotSame($tpl->invoice_number, $child->invoice_number);
            $this->assertNotNull($child->invoice_number);

            $numbers[] = $child->invoice_number;

            if ($previousDate !== null) {
                $this->assertTrue($child->invoice_date->gte($previousDate)); // ascending dates
            }
            $previousDate = $child->invoice_date;
        }

        $this->assertSame($numbers, array_values(array_unique($numbers))); // all unique

        $tpl->refresh();
        $this->assertTrue($tpl->last_generated->isSameDay(today())); // advanced to last occurrence

        // Idempotent: an immediate re-run has nothing new due.
        $this->assertSame(0, $tpl->generateDue());
        $this->assertCount(4, $this->children($tpl));
    }

    public function test_recurrence_end_caps_generation(): void
    {
        // Occurrence 2 = 2026-04-15, occurrence 3 = 2026-05-15; end sits
        // between them, so only occurrences 1 and 2 are generated.
        $tpl = $this->makeTemplate(['recurrence_end' => Carbon::parse('2026-04-20')]);

        $this->assertSame(2, $tpl->generateDue());
        $this->assertCount(2, $this->children($tpl));
    }

    public function test_generated_children_inherit_the_template_team(): void
    {
        // FKs are enforced in this suite and invoices.team_id references teams,
        // so a real team row is required (teams.user_id has no FK — 1 is fine).
        $team = Team::forceCreate(['user_id' => 1, 'name' => 'Recurring Co', 'personal_team' => false]);

        $tpl = $this->makeTemplate(['team_id' => $team->id]);

        $tpl->generateDue();

        $children = $this->children($tpl);
        $this->assertNotEmpty($children);
        foreach ($children as $child) {
            $this->assertSame($team->id, (int) $child->team_id);
        }
    }

    public function test_generation_does_not_post_to_the_general_ledger_or_notify(): void
    {
        Notification::fake();

        $tpl = $this->makeTemplate();

        $tpl->generateDue();

        // Drafts: nothing posted to the GL and nobody emailed/notified.
        $this->assertSame(0, JournalEntry::count());
        Notification::assertNothingSent();
    }
}
