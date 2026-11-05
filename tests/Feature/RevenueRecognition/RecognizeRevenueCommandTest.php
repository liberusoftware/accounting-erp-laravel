<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueRecognition;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\InvoicePostingService;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;
use Tests\TestCase;

class RecognizeRevenueCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_recognizes_all_active_schedules(): void
    {
        Carbon::setTestNow('2026-02-20'); // periods 01-15, 02-15 due
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 1200, 'invoice_date' => '2026-01-15']);
        Account::create(['account_number' => 1100, 'account_name' => 'AR', 'account_type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        $deferred = Account::create(['account_number' => 2400, 'account_name' => 'Deferred', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        $revenue = Account::create(['account_number' => 4000, 'account_name' => 'Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id, 'team_id' => $team->id]);
        app(RevenueRecognitionService::class)->createFromInvoice($invoice, 12, $deferred, $revenue);
        app(InvoicePostingService::class)->post($invoice->fresh());

        $this->artisan('revenue:recognize')->assertSuccessful();

        // 1 posting entry + 2 recognised periods (01-15, 02-15 due by 2026-02-20)
        $this->assertSame(3, JournalEntry::where('team_id', $team->id)->count());
        Carbon::setTestNow();
    }

    public function test_a_locked_schedule_does_not_starve_later_schedules(): void
    {
        Carbon::setTestNow('2026-03-01'); // 01-15, 02-15 due
        // Team A (created first, lower id): posted BEFORE the books lock is applied (so posting
        // itself succeeds), then locked -> recognition throws when it tries to book period 1.
        $userA = User::factory()->create();
        $teamA = Team::forceCreate(['user_id' => $userA->id, 'name' => 'A', 'personal_team' => false]);
        $invA = Invoice::factory()->create(['team_id' => $teamA->id, 'total_amount' => 1200, 'invoice_date' => '2026-01-15']);
        Account::create(['account_number' => 1100, 'account_name' => 'AR A', 'account_type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userA->id, 'team_id' => $teamA->id]);
        $defA = Account::create(['account_number' => 2401, 'account_name' => 'Def A', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userA->id, 'team_id' => $teamA->id]);
        $revA = Account::create(['account_number' => 4001, 'account_name' => 'Rev A', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userA->id, 'team_id' => $teamA->id]);
        app(RevenueRecognitionService::class)->createFromInvoice($invA, 12, $defA, $revA);
        app(InvoicePostingService::class)->post($invA->fresh());
        $teamA->update(['books_locked_before' => '2026-06-01']); // lock AFTER posting, before recognizing

        // Team B (clean): should get recognized despite A throwing.
        $userB = User::factory()->create();
        $teamB = Team::forceCreate(['user_id' => $userB->id, 'name' => 'B', 'personal_team' => false]);
        $invB = Invoice::factory()->create(['team_id' => $teamB->id, 'total_amount' => 1200, 'invoice_date' => '2026-01-15']);
        Account::create(['account_number' => 1100, 'account_name' => 'AR B', 'account_type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userB->id, 'team_id' => $teamB->id]);
        $defB = Account::create(['account_number' => 2402, 'account_name' => 'Def B', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userB->id, 'team_id' => $teamB->id]);
        $revB = Account::create(['account_number' => 4002, 'account_name' => 'Rev B', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $userB->id, 'team_id' => $teamB->id]);
        app(RevenueRecognitionService::class)->createFromInvoice($invB, 12, $defB, $revB);
        app(InvoicePostingService::class)->post($invB->fresh());

        $this->artisan('revenue:recognize')->assertSuccessful();

        // Team A: 1 posting entry only — recognition throws (locked books) and is skipped.
        $this->assertSame(1, JournalEntry::where('team_id', $teamA->id)->count());
        // Team B: 1 posting entry + 2 recognised periods — NOT starved by A's failure.
        $this->assertSame(3, JournalEntry::where('team_id', $teamB->id)->count());
        Carbon::setTestNow();
    }
}
