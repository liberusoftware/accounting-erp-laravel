<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CustomerPayments\Actions\AllocateCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\ReconcileCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\RecordCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\RefundCustomerPayment;
use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentStatus;
use Liberu\Accounting\CustomerPayments\Exceptions\InvalidCustomerPayment;

uses(RefreshDatabase::class);

it('allocates a partial payment, refunds the balance and reconciles it', function (): void {
    $payment = app(RecordCustomerPayment::class)->handle(['team_id' => 61, 'customer_id' => 'cust-1', 'kind' => 'receipt', 'reference' => 'REC-1', 'currency' => 'GBP', 'amount' => 500, 'gateway_reference' => 'gw-1']);
    app(AllocateCustomerPayment::class)->handle($payment, 'INV-1', 300);
    app(RefundCustomerPayment::class)->handle($payment->refresh(), 100);
    $reconciled = app(ReconcileCustomerPayment::class)->handle($payment->refresh(), 'DEP-1');
    expect($reconciled->status)->toBe(CustomerPaymentStatus::Reconciled)->and((float) $reconciled->allocated_amount)->toBe(300.0)->and((float) $reconciled->refunded_amount)->toBe(100.0);
});

it('rejects an allocation beyond the unapplied balance', function (): void {
    $payment = app(RecordCustomerPayment::class)->handle(['team_id' => 61, 'customer_id' => 'cust-1', 'kind' => 'receipt', 'reference' => 'REC-2', 'currency' => 'GBP', 'amount' => 50]);
    expect(fn () => app(AllocateCustomerPayment::class)->handle($payment, 'INV-2', 51))->toThrow(InvalidCustomerPayment::class);
});
