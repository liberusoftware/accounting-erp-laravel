<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PayrollIntegration\Actions\ImportPayrollRun;
use Liberu\Accounting\PayrollIntegration\Actions\MarkPayrollImport;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Exceptions\InvalidPayrollImport;
use Liberu\Accounting\PayrollIntegration\Queries\PayrollImportSummary;

uses(RefreshDatabase::class);
it('validates and idempotently imports provider runs through reconciliation', function (): void {
    $attributes = ['team_id' => 1, 'provider' => 'provider-a', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'run_ref' => 'RUN-1', 'currency' => 'GBP', 'employee_refs' => ['E-1'], 'dimensions' => ['department' => 'sales'], 'project_refs' => ['P-1']];
    $import = app(ImportPayrollRun::class)->handle($attributes);
    $same = app(ImportPayrollRun::class)->handle($attributes);
    expect($same->id)->toBe($import->id)->and($import->status)->toBe(ImportStatus::Validated);
    $import = app(MarkPayrollImport::class)->handle($import, ImportStatus::Imported);
    $import = app(MarkPayrollImport::class)->handle($import, ImportStatus::Reconciled);
    expect($import->status)->toBe(ImportStatus::Reconciled)->and(app(PayrollImportSummary::class)->forTeam(1)['reconciled'])->toBe(1);
});
it('rejects missing identity and invalid status transitions', function (): void {
    expect(fn () => app(ImportPayrollRun::class)->handle(['provider' => 'x', 'run_ref' => 'r']))->toThrow(InvalidPayrollImport::class);
    $import = app(ImportPayrollRun::class)->handle(['team_id' => 1, 'provider' => 'x', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'run_ref' => 'r', 'employee_refs' => ['e']]);
    expect(fn () => app(MarkPayrollImport::class)->handle($import, ImportStatus::Reconciled))->toThrow(InvalidPayrollImport::class);
});
