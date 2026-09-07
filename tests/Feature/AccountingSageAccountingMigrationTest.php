<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\SageAccountingMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\SageAccountingMigration\Actions\ImportMigrationRecords;
use Liberu\Accounting\SageAccountingMigration\Actions\ReconcileMigration;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRunStatus;
use Liberu\Accounting\SageAccountingMigration\Exceptions\InvalidMigration;

uses(RefreshDatabase::class);

it('imports all provider-neutral entity families idempotently and reconciles the run', function (): void {
    $run = app(CreateMigrationRun::class)->handle(null, ['source' => 'sage']);
    $records = collect(['chart_analysis_type', 'contact', 'product', 'sale', 'purchase', 'payment', 'bank', 'vat_cis_evidence', 'currency', 'budget', 'attachment'])->map(fn (string $entity): array => ['entity_type' => $entity, 'source_id' => 'sage-'.$entity, 'payload' => ['name' => $entity]])->all();
    $imported = app(ImportMigrationRecords::class)->handle($run, $records);
    expect($imported->status)->toBe(MigrationRunStatus::Completed)->and($imported->records)->toHaveCount(11);
    $rerun = app(ImportMigrationRecords::class)->handle($imported, $records);
    expect($rerun->records)->toHaveCount(11)->and($rerun->imported_records)->toBe(11);
    expect(app(ReconcileMigration::class)->handle($rerun)->status)->toBe(MigrationRunStatus::Reconciled);
});

it('rejects malformed untrusted source records transactionally', function (): void {
    $run = app(CreateMigrationRun::class)->handle(null);
    expect(fn () => app(ImportMigrationRecords::class)->handle($run, [['entity_type' => 'contact', 'source_id' => 'c-1', 'payload' => 'not-an-array']]))->toThrow(InvalidMigration::class);
    expect($run->records()->count())->toBe(0);
});
