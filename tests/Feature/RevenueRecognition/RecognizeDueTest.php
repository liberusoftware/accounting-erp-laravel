<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueRecognition;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\InvoicePostingService;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;
use Tests\TestCase;

class RecognizeDueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-03-20'); // periods 01-15, 02-15, 03-15 are due; 04-15+ not yet
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @return array{0:RevenueSchedule,1:Account,2:Account,3:Team} */
    private function schedule(): array
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 1200, 'invoice_date' => '2026-01-15']);
        Account::create(['account_number' => 1100, 'account_name' => 'AR', 'account_type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        $deferred = Account::create(['account_number' => 2400, 'account_name' => 'Deferred', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        $revenue = Account::create(['account_number' => 4000, 'account_name' => 'Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        $schedule = app(RevenueRecognitionService::class)->createFromInvoice($invoice, 12, $deferred, $revenue);

        app(InvoicePostingService::class)->post($invoice->fresh());

        return [$schedule, $deferred, $revenue, $team];
    }

    public function test_recognizes_only_due_periods_posts_balanced_entries_and_is_idempotent(): void
    {
        [$schedule, $deferred, $revenue, $team] = $this->schedule();

        $count = app(RevenueRecognitionService::class)->recognizeDue($schedule);

        $this->assertSame(3, $count); // 01-15, 02-15, 03-15 <= 2026-03-20
        // 1 posting entry + 3 recognition entries, each posted/balanced, each stamped with the team's owner
        $entries = JournalEntry::where('team_id', $team->id)->get();
        $this->assertCount(4, $entries);
        foreach ($entries as $je) {
            $this->assertTrue($je->is_posted);
            $this->assertTrue($je->isBalanced());
            $this->assertSame($team->user_id, (int) $je->user_id);
        }
        // posting credited Deferred by the full 1200 total; recognising 3 periods (3*100.00)
        // debits it back down: 1200.00 - 300.00 = 900.00. Revenue recognised = 300.00.
        $this->assertSame('300.00', number_format((float) $revenue->fresh()->balance, 2, '.', ''));
        $this->assertSame('900.00', number_format((float) $deferred->fresh()->balance, 2, '.', ''));
        // each recognised entry links its journal entry
        $this->assertSame(3, $schedule->entries()->whereNotNull('journal_entry_id')->where('recognized', true)->count());

        // Idempotent: re-run today generates nothing new.
        $this->assertSame(0, app(RevenueRecognitionService::class)->recognizeDue($schedule->fresh()));
    }

    public function test_full_recognition_marks_schedule_completed(): void
    {
        [$schedule] = $this->schedule();
        Carbon::setTestNow('2027-06-01'); // all 12 periods now due

        app(RevenueRecognitionService::class)->recognizeDue($schedule);

        $this->assertSame('completed', $schedule->fresh()->status);
        $this->assertSame(12, $schedule->entries()->where('recognized', true)->count());
    }
}
