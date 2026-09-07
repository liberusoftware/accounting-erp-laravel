<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ContractorReporting\Actions\CorrectContractorReport;
use Liberu\Accounting\ContractorReporting\Actions\CreateContractorReport;
use Liberu\Accounting\ContractorReporting\Actions\FileContractorReport;
use Liberu\Accounting\ContractorReporting\Actions\ValidateContractorReport;
use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;
use Liberu\Accounting\ContractorReporting\Exceptions\InvalidContractorReport;

uses(RefreshDatabase::class);

it('validates, files and corrects a contractor report', function (): void {
    $report = app(CreateContractorReport::class)->handle([
        'team_id' => 101,
        'payee_ref' => 'payee-1',
        'tax_year' => 2026,
        'classification' => 'nonemployee',
        'threshold' => 600,
        'reportable_amount' => 1000,
        'form_type' => '1099-NEC',
    ]);

    app(ValidateContractorReport::class)->handle($report, ['tax_id' => '12-3456789', 'legal_name' => 'Acme Contractor']);
    app(FileContractorReport::class)->handle($report->refresh(), 'test-electronic-filing');
    $corrected = app(CorrectContractorReport::class)->handle($report->refresh(), ['reason' => 'Amount updated']);

    expect($corrected->status)->toBe(ContractorReportStatus::Corrected)
        ->and($corrected->payee_validation)->toMatchArray(['tax_id' => '12-3456789'])
        ->and($corrected->correction)->toBe(['reason' => 'Amount updated']);
});

it('rejects invalid amounts and invalid state transitions', function (): void {
    expect(fn () => app(CreateContractorReport::class)->handle([
        'team_id' => 101,
        'payee_ref' => 'payee-2',
        'tax_year' => 26,
        'classification' => 'nonemployee',
        'threshold' => -1,
        'form_type' => '1099-NEC',
    ]))->toThrow(InvalidContractorReport::class);

    $report = app(CreateContractorReport::class)->handle([
        'team_id' => 101,
        'payee_ref' => 'payee-3',
        'tax_year' => 2026,
        'classification' => 'nonemployee',
        'threshold' => 600,
        'form_type' => '1099-NEC',
    ]);

    expect(fn () => app(FileContractorReport::class)->handle($report, 'adapter'))
        ->toThrow(InvalidContractorReport::class);
});
