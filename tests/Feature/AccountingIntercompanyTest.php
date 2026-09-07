<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Intercompany\Actions\AddTransferPricingEvidence;
use Liberu\Accounting\Intercompany\Actions\ConfirmTransaction;
use Liberu\Accounting\Intercompany\Actions\CreateCounterparty;
use Liberu\Accounting\Intercompany\Actions\CreateTransaction;
use Liberu\Accounting\Intercompany\Actions\ReconcileIntercompany;
use Liberu\Accounting\Intercompany\Actions\SettleTransaction;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Queries\IntercompanyQuery;
use Tests\TestCase;

final class AccountingIntercompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_can_be_confirmed_evidenced_settled_and_summarized(): void
    {
        $counterparty = app(CreateCounterparty::class)->handle(['entity_ref' => 'UK', 'counterparty_ref' => 'DE', 'name' => 'German entity', 'default_currency' => 'EUR']);
        $transaction = app(CreateTransaction::class)->handle($counterparty, ['transaction_ref' => 'IC-001', 'source_entity_ref' => 'UK', 'target_entity_ref' => 'DE', 'transaction_type' => 'service', 'description' => 'Shared services', 'amount' => 1000, 'currency' => 'EUR']);
        $transaction = app(ConfirmTransaction::class)->handle($transaction, 'DE', true);
        app(AddTransferPricingEvidence::class)->handle($transaction, ['evidence_ref' => 'TP-001', 'kind' => 'benchmark', 'currency' => 'EUR', 'arm_length_value' => 1000]);
        $transaction = app(SettleTransaction::class)->handle($transaction, ['settlement_ref' => 'SET-001', 'amount' => 1000, 'source_ref' => 'BANK-001']);

        $summary = app(IntercompanyQuery::class)->reconciliationSummary($transaction);
        $this->assertSame(TransactionStatus::Settled, $transaction->status);
        $this->assertSame(1000.0, $summary['settled_amount']);
        $this->assertSame(0.0, $summary['outstanding']);
    }

    public function test_unconfirmed_transaction_cannot_be_settled(): void
    {
        $counterparty = app(CreateCounterparty::class)->handle(['entity_ref' => 'UK', 'counterparty_ref' => 'FR', 'name' => 'French entity', 'default_currency' => 'EUR']);
        $transaction = app(CreateTransaction::class)->handle($counterparty, ['transaction_ref' => 'IC-002', 'source_entity_ref' => 'UK', 'target_entity_ref' => 'FR', 'transaction_type' => 'goods', 'description' => 'Goods', 'amount' => 500, 'currency' => 'EUR']);
        $this->expectException(InvalidIntercompany::class);
        app(SettleTransaction::class)->handle($transaction, ['settlement_ref' => 'SET-002', 'amount' => 500, 'source_ref' => 'BANK-002']);
    }

    public function test_reconciliation_marks_variance(): void
    {
        $reconciliation = app(ReconcileIntercompany::class)->handle(['reconciliation_ref' => 'REC-001', 'period_ref' => '2026-08', 'entity_ref' => 'UK', 'counterparty_ref' => 'DE', 'source_total' => 1000, 'counterparty_total' => 990]);
        $this->assertSame('variance', $reconciliation->status);
        $this->assertSame('10.00', $reconciliation->difference_total);
    }
}
