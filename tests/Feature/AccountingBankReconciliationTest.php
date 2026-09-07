<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Liberu\Accounting\BankReconciliation\Actions\AddReconciliationEntry;
use Liberu\Accounting\BankReconciliation\Actions\ConfirmReconciliationEntry;
use Liberu\Accounting\BankReconciliation\Actions\CreateReconciliationSession;
use Liberu\Accounting\BankReconciliation\Actions\SignOffReconciliation;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationStatus;
use Liberu\Accounting\BankReconciliation\Events\ReconciliationSignedOff;
use Liberu\Accounting\BankReconciliation\Exceptions\InvalidReconciliation;
use Tests\TestCase;

final class AccountingBankReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_entries_can_be_confirmed_and_a_zero_variance_session_signed_off(): void
    {
        Event::fake([ReconciliationSignedOff::class]);
        $session = app(CreateReconciliationSession::class)->handle(['team_id' => 1, 'bank_account_id' => 77, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'opening_balance' => 100, 'statement_balance' => 150]);
        $entry = app(AddReconciliationEntry::class)->handle($session, ['source_type' => 'bank-feed-transaction', 'source_id' => 'txn-1', 'kind' => 'match', 'amount' => 50, 'currency' => 'usd', 'confidence' => 0.98]);

        app(ConfirmReconciliationEntry::class)->handle($entry);
        $signed = app(SignOffReconciliation::class)->handle($session);

        $this->assertSame(ReconciliationStatus::SignedOff, $signed->status);
        $this->assertNotNull($signed->signed_off_at);
        Event::assertDispatched(ReconciliationSignedOff::class);
        $this->assertDatabaseHas('accounting_bank_reconciliation_entries', ['id' => $entry->id, 'status' => 'confirmed', 'currency' => 'USD']);
    }

    public function test_duplicate_sources_and_non_zero_variances_cannot_be_signed_off(): void
    {
        $session = app(CreateReconciliationSession::class)->handle(['bank_account_id' => 88, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'opening_balance' => 100, 'statement_balance' => 200]);
        $attributes = ['source_type' => 'bank-feed-transaction', 'source_id' => 'txn-duplicate', 'kind' => 'match', 'amount' => 50, 'currency' => 'USD'];
        app(AddReconciliationEntry::class)->handle($session, $attributes);
        $this->expectException(InvalidReconciliation::class);
        app(AddReconciliationEntry::class)->handle($session, $attributes);
    }
}
