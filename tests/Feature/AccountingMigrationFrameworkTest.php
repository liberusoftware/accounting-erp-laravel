<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\MigrationFramework\Actions\CreateBatch;
use Liberu\Accounting\MigrationFramework\Actions\CreateMapping;
use Liberu\Accounting\MigrationFramework\Actions\ReconcileBatch;
use Liberu\Accounting\MigrationFramework\Actions\RegisterSource;
use Liberu\Accounting\MigrationFramework\Actions\RunBatch;
use Liberu\Accounting\MigrationFramework\Enums\MigrationStatus;
use Liberu\Accounting\MigrationFramework\Enums\RowStatus;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Queries\MigrationQuery;

uses(RefreshDatabase::class);

it('registers a source, maps it, dry-runs, resumes, and reconciles a batch', function (): void {
    $source = app(RegisterSource::class)->handle(['team_id' => 1, 'source_ref' => 'legacy-erp', 'provider' => 'legacy', 'source_type' => 'csv', 'name' => 'Legacy ERP']);
    $mapping = app(CreateMapping::class)->handle($source, ['mapping_ref' => 'customers-v1', 'entity_type' => 'customer', 'field_map' => ['name' => 'customer_name'], 'transforms' => ['name' => ['operation' => 'uppercase']]]);
    $batch = app(CreateBatch::class)->handle($source, $mapping, ['team_id' => 1, 'batch_ref' => 'IMPORT-1'], [['source_key' => 'c-1', 'payload' => ['name' => ' acme ']], ['source_key' => 'c-2', 'payload' => ['name' => 'beta']]]);
    $dryRun = app(RunBatch::class)->handle($batch, true);
    expect($dryRun->status)->toBe(MigrationStatus::DryRun)->and($dryRun->rows()->where('status', RowStatus::Valid)->count())->toBe(2);
    $completed = app(RunBatch::class)->handle($dryRun, false);
    $reconciliation = app(ReconcileBatch::class)->handle($completed, ['source_total' => 2, 'destination_total' => 2]);
    expect($reconciliation->status)->toBe('matched')->and($completed->fresh()->status)->toBe(MigrationStatus::Reconciled)->and(app(MigrationQuery::class)->counts($completed)['remaining'])->toBe(0);
});

it('rejects invalid or conflicting batch inputs', function (): void {
    $source = app(RegisterSource::class)->handle(['source_ref' => 'legacy', 'provider' => 'csv', 'source_type' => 'file', 'name' => 'Legacy']);
    $mapping = app(CreateMapping::class)->handle($source, ['mapping_ref' => 'accounts', 'entity_type' => 'account', 'field_map' => ['code' => 'code']]);
    expect(fn (): mixed => app(CreateBatch::class)->handle($source, $mapping, ['batch_ref' => 'BAD'], [['payload' => ['x' => 1]]]))->toThrow(InvalidMigration::class);
    $data = ['batch_ref' => 'B-1'];
    $rows = [['source_key' => '1', 'payload' => ['x' => 1]]];
    $first = app(CreateBatch::class)->handle($source, $mapping, $data, $rows);
    $same = app(CreateBatch::class)->handle($source, $mapping, $data, $rows);
    expect($same->id)->toBe($first->id);
    expect(fn (): mixed => app(CreateBatch::class)->handle($source, $mapping, $data, [['source_key' => '1', 'payload' => ['x' => 2]]]))->toThrow(InvalidMigration::class);
});
