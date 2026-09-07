<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\XeroMigration\Actions\ConnectXeroTenant;
use Liberu\Accounting\XeroMigration\Actions\RecordMigration;
use Liberu\Accounting\XeroMigration\Enums\MigrationRecordStatus;
use Liberu\Accounting\XeroMigration\Enums\XeroConnectionStatus;
use Liberu\Accounting\XeroMigration\Exceptions\InvalidXeroMigration;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;

uses(RefreshDatabase::class);

it('connects a tenant with encrypted credentials and records migrations', function (): void {
    $connection = app(ConnectXeroTenant::class)->handle([
        'team_id' => 31,
        'tenant_ref' => 'tenant-31',
        'access_token' => 'access-token',
        'refresh_token' => 'refresh-token',
    ]);

    $record = app(RecordMigration::class)->handle($connection, [
        'source_type' => 'invoice',
        'source_id' => 'xero-invoice-1',
    ]);

    expect($connection->fresh()->status)->toBe(XeroConnectionStatus::Active)
        ->and($connection->fresh()->access_token)->toBe('access-token')
        ->and($record->fresh()->team_id)->toBe(31)
        ->and($record->fresh()->status)->toBe(MigrationRecordStatus::Migrated)
        ->and(XeroConnection::query()->firstOrFail()->toArray())->not->toHaveKey('access_token');
});

it('updates a previously recorded source instead of duplicating it', function (): void {
    $connection = app(ConnectXeroTenant::class)->handle(['team_id' => 31, 'tenant_ref' => 'tenant-31', 'access_token' => 'access-token']);
    app(RecordMigration::class)->handle($connection, ['source_type' => 'invoice', 'source_id' => 'xero-invoice-1']);
    app(RecordMigration::class)->handle($connection->fresh(), ['source_type' => 'invoice', 'source_id' => 'xero-invoice-1', 'status' => MigrationRecordStatus::Failed, 'error' => 'retry']);

    expect($connection->migrationRecords()->count())->toBe(1)
        ->and($connection->migrationRecords()->firstOrFail()->status)->toBe(MigrationRecordStatus::Failed);
});

it('rejects a connection without credentials', function (): void {
    expect(fn (): mixed => app(ConnectXeroTenant::class)->handle(['team_id' => 31, 'tenant_ref' => 'tenant-31']))
        ->toThrow(InvalidXeroMigration::class);
});
