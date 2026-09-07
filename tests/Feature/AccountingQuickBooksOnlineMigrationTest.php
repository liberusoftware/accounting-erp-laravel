<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\ImportMigrationRecords;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\ReconcileMigration;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\MigrationStatus;
use Liberu\Accounting\QuickBooksOnlineMigration\Exceptions\InvalidMigration;

uses(RefreshDatabase::class);
it('imports all QBO entity families idempotently and reconciles', function (): void {
    $run = app(CreateMigrationRun::class)->handle(null, ['realm_id' => 'realm-1']);
    $types = ['chart', 'class', 'location', 'contact', 'item', 'invoice', 'bill', 'credit', 'payment', 'journal', 'bank', 'project', 'tax', 'attachment', 'source_id'];
    $records = array_map(fn (string $type): array => ['entity_type' => $type, 'source_id' => 'qbo-'.$type, 'payload' => ['name' => $type]], $types);
    $run = app(ImportMigrationRecords::class)->handle($run, $records);
    expect($run->status)->toBe(MigrationStatus::Completed)->and($run->records)->toHaveCount(15);
    $run = app(ImportMigrationRecords::class)->handle($run, $records);
    expect($run->records)->toHaveCount(15)->and(app(ReconcileMigration::class)->handle($run)->status)->toBe(MigrationStatus::Reconciled);
});
it('rejects malformed untrusted QBO records transactionally', function (): void {
    $run = app(CreateMigrationRun::class)->handle(null);
    expect(fn () => app(ImportMigrationRecords::class)->handle($run, [['entity_type' => 'invoice', 'source_id' => '1', 'payload' => 'bad']]))->toThrow(InvalidMigration::class)->and($run->records()->count())->toBe(0);
});
