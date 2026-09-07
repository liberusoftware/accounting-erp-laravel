<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PurchaseRequisitions\Actions\CreateRequisition;
use Liberu\Accounting\PurchaseRequisitions\Actions\RecordApproval;
use Liberu\Accounting\PurchaseRequisitions\Actions\TransitionRequisition;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Exceptions\InvalidRequisition;

uses(RefreshDatabase::class);
it('submits, approves, hands off, and converts a requisition', function (): void {
    $requisition = app(CreateRequisition::class)->handle(['requester_ref' => 'employee-1', 'currency' => 'USD', 'total_amount' => 125, 'lines' => [['description' => 'Laptop', 'amount' => 125]], 'coding' => ['account_ref' => 'office']]);
    app(TransitionRequisition::class)->handle($requisition, RequisitionStatus::Submitted);
    app(RecordApproval::class)->handle($requisition->refresh(), ['approver_ref' => 'manager-1', 'decision' => 'approved']);
    app(TransitionRequisition::class)->handle($requisition->refresh(), RequisitionStatus::Sourcing, ['sourcing_ref' => 'sourcing-1']);
    $converted = app(TransitionRequisition::class)->handle($requisition->refresh(), RequisitionStatus::Converted, ['converted_ref' => 'PO-1']);
    expect($converted->status)->toBe(RequisitionStatus::Converted)->and($converted->converted_ref)->toBe('PO-1');
});
it('rejects invalid amounts and illegal status transitions', function (): void {
    expect(fn () => app(CreateRequisition::class)->handle(['requester_ref' => 'employee-1', 'currency' => 'USD', 'total_amount' => 0, 'lines' => [['amount' => 0]]]))->toThrow(InvalidRequisition::class);
    $requisition = app(CreateRequisition::class)->handle(['requester_ref' => 'employee-1', 'currency' => 'USD', 'total_amount' => 10, 'lines' => [['amount' => 10]]]);
    expect(fn () => app(TransitionRequisition::class)->handle($requisition, RequisitionStatus::Converted))->toThrow(InvalidRequisition::class);
});
