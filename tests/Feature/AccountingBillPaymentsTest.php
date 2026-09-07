<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\BillPayments\Actions\ApproveBillPayment;
use Liberu\Accounting\BillPayments\Actions\CreateBillPaymentProposal;
use Liberu\Accounting\BillPayments\Actions\RequestBillPaymentApproval;
use Liberu\Accounting\BillPayments\Actions\SubmitBillPayment;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Queries\BillPaymentOptimizationQuery;
use Liberu\Foundation\Integrations\Contracts\PaymentProviderAdapter;
use Tests\TestCase;

final class AccountingBillPaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_proposal_approval_optimization_and_provider_submission(): void
    {
        $payment = app(CreateBillPaymentProposal::class)->handle(['team_id' => 1, 'supplier_id' => 44, 'bill_reference' => 'BILL-100', 'amount' => 1000, 'currency' => 'usd', 'due_date' => '2026-09-30', 'discount_date' => '2026-09-10', 'discount_rate' => 2, 'bank_details' => ['beneficiary_name' => 'Supplier', 'iban' => 'DE123']]);
        $optimization = app(BillPaymentOptimizationQuery::class)->handle($payment);
        $this->assertSame('2026-09-10', $optimization['recommended_date']);
        $this->assertSame(20.0, $optimization['discount_amount']);
        $this->assertNull($payment->toArray()['bank_details'] ?? null);

        app(RequestBillPaymentApproval::class)->handle($payment);
        app(ApproveBillPayment::class)->handle($payment->fresh());
        $adapter = new class() implements PaymentProviderAdapter
        {
            public function sendPayment(array $payment): array
            {
                return ['id' => 'provider-1'];
            }

            public function sendBulkPayments(string $title, array $payments, ?string $scheduleFor = null): array
            {
                return ['payments' => $payments];
            }
        };
        $submitted = app(SubmitBillPayment::class)->handle($payment->fresh(), $adapter);

        $this->assertSame(BillPaymentStatus::Submitted, $submitted->status);
        $this->assertSame('provider-1', $submitted->provider_reference);
        $this->assertDatabaseHas('accounting_bill_payment_proposals', ['id' => $payment->id, 'status' => 'submitted']);
    }

    public function test_duplicate_supplier_bill_proposals_are_rejected(): void
    {
        $attributes = ['team_id' => 2, 'supplier_id' => 55, 'bill_reference' => 'BILL-200', 'amount' => 25, 'currency' => 'EUR', 'due_date' => '2026-09-30', 'bank_details' => ['beneficiary_name' => 'Supplier', 'account_number' => '123']];
        app(CreateBillPaymentProposal::class)->handle($attributes);
        $this->expectException(InvalidBillPayment::class);
        app(CreateBillPaymentProposal::class)->handle($attributes);
    }
}
