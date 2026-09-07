<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ContractorCompliance\Actions\ExportContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Actions\IssueContractorStatement;
use Liberu\Accounting\ContractorCompliance\Actions\RecordContractorEvidence;
use Liberu\Accounting\ContractorCompliance\Actions\RegisterContractor;
use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;

uses(RefreshDatabase::class);

it('records evidence, issues a statement and exports contractor compliance', function (): void {
    $contractor = app(RegisterContractor::class)->handle(['team_id' => 202, 'contractor_ref' => 'contractor-1', 'legal_name' => 'Acme Contractor', 'classification' => 'cis', 'withholding_scheme' => 'standard']);
    app(RecordContractorEvidence::class)->handle($contractor, ['type' => 'certificate', 'reference' => 'cert-1']);
    app(IssueContractorStatement::class)->handle($contractor->refresh(), ['period' => '2026-Q3', 'amount' => 1250]);
    $exported = app(ExportContractorCompliance::class)->handle($contractor->refresh(), 'GB');

    expect($exported->evidence)->toHaveCount(1)
        ->and($exported->statement['period'])->toBe('2026-Q3')
        ->and($exported->regional_export['region'])->toBe('GB');
});

it('rejects incomplete evidence', function (): void {
    $contractor = app(RegisterContractor::class)->handle(['team_id' => 202, 'contractor_ref' => 'contractor-2', 'legal_name' => 'Another Contractor', 'classification' => '1099']);

    expect(fn () => app(RecordContractorEvidence::class)->handle($contractor, ['type' => 'certificate']))
        ->toThrow(InvalidContractorCompliance::class);
});
