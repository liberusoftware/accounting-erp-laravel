<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PayrollLiabilities\Actions\AllocatePayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Actions\RecordPayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Enums\LiabilityStatus;
use Liberu\Accounting\PayrollLiabilities\Exceptions\InvalidLiability;
use Liberu\Accounting\PayrollLiabilities\Queries\PayrollLiabilitySummary;

uses(RefreshDatabase::class);
it('records a liability and allocates payments without exceeding the balance', function (): void {
    $liability = app(RecordPayrollLiability::class)->handle(['team_id' => 1, 'liability_ref' => 'TAX-1', 'agency_ref' => 'HMRC', 'amount' => 500, 'currency' => 'GBP', 'due_on' => '2026-09-01']);
    $liability = app(AllocatePayrollLiability::class)->handle($liability, 200, 'PAY-1');
    expect($liability->status)->toBe(LiabilityStatus::PartPaid);
    $liability = app(AllocatePayrollLiability::class)->handle($liability, 300, 'PAY-2');
    expect($liability->status)->toBe(LiabilityStatus::Paid)->and(app(PayrollLiabilitySummary::class)->forTeam(1)['outstanding'])->toBe(0.0);
});
it('rejects invalid amounts and over-allocation', function (): void {
    expect(fn () => app(RecordPayrollLiability::class)->handle(['team_id' => 1, 'liability_ref' => 'BAD', 'amount' => 0]))->toThrow(InvalidLiability::class);
    $liability = app(RecordPayrollLiability::class)->handle(['team_id' => 1, 'liability_ref' => 'TAX-2', 'amount' => 100]);
    expect(fn () => app(AllocatePayrollLiability::class)->handle($liability, 101, 'PAY'))->toThrow(InvalidLiability::class);
});
