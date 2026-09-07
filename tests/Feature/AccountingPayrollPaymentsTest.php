<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PayrollPayments\Actions\CreatePayrollPaymentBatch;
use Liberu\Accounting\PayrollPayments\Actions\TransitionPayrollPayment;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Exceptions\InvalidPayrollPayment;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;
use Liberu\Accounting\PayrollPayments\Queries\PayrollPaymentSummary;

uses(RefreshDatabase::class);
it('creates idempotent batches and supports approval provider settlement and reconciliation', function (): void {
    $batch = app(CreatePayrollPaymentBatch::class)->handle(['team_id' => 1, 'batch_ref' => 'PAY-1', 'net_pay_amount' => 1000, 'liability_amount' => 200, 'currency' => 'GBP']);
    $same = app(CreatePayrollPaymentBatch::class)->handle(['team_id' => 1, 'batch_ref' => 'PAY-1', 'net_pay_amount' => 1200, 'liability_amount' => 250, 'currency' => 'GBP']);
    expect($same->id)->toBe($batch->id);
    $action = app(TransitionPayrollPayment::class);
    foreach ([PaymentStatus::Approved, PaymentStatus::Submitted, PaymentStatus::Settled, PaymentStatus::Reconciled] as $status) {
        $batch = $action->handle($batch, $status);
    }expect($batch->status)->toBe(PaymentStatus::Reconciled)->and(app(PayrollPaymentSummary::class)->forTeam(1)['reconciled'])->toBe(1);
});
it('rejects invalid batch amounts and transitions', function (): void {
    expect(fn () => app(CreatePayrollPaymentBatch::class)->handle(['team_id' => 1, 'batch_ref' => 'BAD', 'net_pay_amount' => 0, 'liability_amount' => 0]))->toThrow(InvalidPayrollPayment::class);
    $batch = PayrollPaymentBatch::create(['team_id' => 1, 'batch_ref' => 'PAY-2', 'net_pay_amount' => 100, 'liability_amount' => 0, 'status' => PaymentStatus::Settled]);
    expect(fn () => app(TransitionPayrollPayment::class)->handle($batch, PaymentStatus::Approved))->toThrow(InvalidPayrollPayment::class);
});
