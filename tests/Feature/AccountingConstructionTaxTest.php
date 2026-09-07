<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ConstructionTax\Actions\CorrectConstructionTaxReturn;
use Liberu\Accounting\ConstructionTax\Actions\CreateConstructionTaxRecord;
use Liberu\Accounting\ConstructionTax\Actions\IssueConstructionTaxStatement;
use Liberu\Accounting\ConstructionTax\Actions\SubmitConstructionTaxReturn;
use Liberu\Accounting\ConstructionTax\Actions\VerifySubcontractor;
use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;
use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;

uses(RefreshDatabase::class);

it('verifies, states, submits and corrects a construction tax return', function (): void {
    $record = app(CreateConstructionTaxRecord::class)->handle(['team_id' => 303, 'subcontractor_ref' => 'sub-1', 'tax_period' => '2026-08', 'deduction_rate' => 20, 'gross_amount' => 1000]);
    app(VerifySubcontractor::class)->handle($record, ['reference' => 'verify-1']);
    app(IssueConstructionTaxStatement::class)->handle($record->refresh(), ['reference' => 'statement-1']);
    app(SubmitConstructionTaxReturn::class)->handle($record->refresh(), 'cis-adapter');
    $corrected = app(CorrectConstructionTaxReturn::class)->handle($record->refresh(), ['reason' => 'Rate corrected']);

    expect($corrected->verification_status)->toBe(ConstructionTaxStatus::Corrected)
        ->and((float) $corrected->deduction_amount)->toBe(200.0)
        ->and($corrected->filing_adapter)->toBe('cis-adapter');
});

it('rejects invalid deduction rates and premature submissions', function (): void {
    expect(fn () => app(CreateConstructionTaxRecord::class)->handle(['team_id' => 303, 'subcontractor_ref' => 'sub-2', 'tax_period' => '2026-08', 'deduction_rate' => 101, 'gross_amount' => 100]))
        ->toThrow(InvalidConstructionTax::class);

    $record = app(CreateConstructionTaxRecord::class)->handle(['team_id' => 303, 'subcontractor_ref' => 'sub-3', 'tax_period' => '2026-09']);

    expect(fn () => app(SubmitConstructionTaxReturn::class)->handle($record, 'cis-adapter'))
        ->toThrow(InvalidConstructionTax::class);
});
