<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\DebtAndLoans\Actions\CreateDebtFacility;
use Liberu\Accounting\DebtAndLoans\Actions\ReconcileDebtMovement;
use Liberu\Accounting\DebtAndLoans\Actions\RecordDebtMovement;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementStatus;
use Liberu\Accounting\DebtAndLoans\Exceptions\InvalidDebt;

uses(RefreshDatabase::class);

it('tracks drawdowns, repayments, charges and reconciliation', function (): void {
    $facility = app(CreateDebtFacility::class)->handle(['team_id' => 22, 'facility_ref' => 'FAC-1', 'lender_ref' => 'BANK-1', 'currency' => 'GBP', 'limit_amount' => 10000, 'interest_rate' => 6.5, 'start_date' => '2026-01-01', 'maturity_date' => '2028-01-01']);
    $drawdown = app(RecordDebtMovement::class)->handle($facility, 'drawdown', ['movement_date' => '2026-08-01', 'principal_amount' => 4000, 'due_date' => '2027-01-01']);
    app(RecordDebtMovement::class)->handle($facility->refresh(), 'interest', ['movement_date' => '2026-08-31', 'interest_amount' => 50]);
    app(RecordDebtMovement::class)->handle($facility->refresh(), 'fee', ['movement_date' => '2026-08-31', 'fee_amount' => 10]);
    app(RecordDebtMovement::class)->handle($facility->refresh(), 'repayment', ['movement_date' => '2026-09-01', 'principal_amount' => 1000]);
    $reconciled = app(ReconcileDebtMovement::class)->handle($drawdown, 'JRN-DEBT-1');

    expect((float) $facility->refresh()->drawn_amount)->toBe(3000.0)->and($reconciled->status)->toBe(DebtMovementStatus::Reconciled);
});

it('rejects drawdowns over the facility limit', function (): void {
    $facility = app(CreateDebtFacility::class)->handle(['team_id' => 22, 'facility_ref' => 'FAC-2', 'lender_ref' => 'BANK-1', 'currency' => 'GBP', 'limit_amount' => 100, 'start_date' => '2026-01-01', 'maturity_date' => '2027-01-01']);
    expect(fn () => app(RecordDebtMovement::class)->handle($facility, 'drawdown', ['movement_date' => '2026-08-29', 'principal_amount' => 101]))->toThrow(InvalidDebt::class);
});
