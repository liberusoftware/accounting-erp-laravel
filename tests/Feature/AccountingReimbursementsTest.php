<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Reimbursements\Actions\CreatePaymentBatch;
use Liberu\Accounting\Reimbursements\Actions\CreateReimbursementLiability;
use Liberu\Accounting\Reimbursements\Actions\ReconcilePaymentBatch;
use Liberu\Accounting\Reimbursements\Actions\UpdatePaymentProviderStatus;
use Liberu\Accounting\Reimbursements\Enums\BatchStatus;
use Liberu\Accounting\Reimbursements\Enums\ReimbursementStatus;
use Liberu\Accounting\Reimbursements\Exceptions\InvalidReimbursement;

uses(RefreshDatabase::class);
it('batches approved liabilities, tracks provider status, remits, and reconciles', function (): void {
    $create = app(CreateReimbursementLiability::class);
    $first = $create->handle(['payee_ref' => 'employee-1', 'currency' => 'USD', 'amount' => 50]);
    $second = $create->handle(['payee_ref' => 'employee-2', 'currency' => 'USD', 'amount' => 25, 'kind' => 'mileage']);
    $batch = app(CreatePaymentBatch::class)->handle([$first->id, $second->id]);
    expect($batch->total_amount)->toEqual('75.00')->and($batch->liabilities)->toHaveCount(2);
    $batch = app(UpdatePaymentProviderStatus::class)->handle($batch, ['status' => 'submitted', 'provider' => 'bank', 'provider_ref' => 'bank-1']);
    expect($batch->status)->toBe(BatchStatus::Submitted)->and($batch->liabilities()->where('status', ReimbursementStatus::Submitted)->count())->toBe(2);
    expect(app(ReconcilePaymentBatch::class)->handle($batch, 75, 'bank-1')->status)->toBe('matched');
});
it('rejects mixed currencies, non-approved liabilities, and reconciliation variance', function (): void {
    $create = app(CreateReimbursementLiability::class);
    $usd = $create->handle(['payee_ref' => 'employee-1', 'currency' => 'USD', 'amount' => 10]);
    $eur = $create->handle(['payee_ref' => 'employee-2', 'currency' => 'EUR', 'amount' => 10]);
    expect(fn () => app(CreatePaymentBatch::class)->handle([$usd->id, $eur->id]))->toThrow(InvalidReimbursement::class);
    $batch = app(CreatePaymentBatch::class)->handle([$usd->id]);
    expect(fn () => app(ReconcilePaymentBatch::class)->handle($batch, 9))->toThrow(InvalidReimbursement::class);
});
