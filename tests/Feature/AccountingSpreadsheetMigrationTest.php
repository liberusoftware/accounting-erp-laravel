<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\SpreadsheetMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\SpreadsheetMigration\Actions\CreateMigrationTemplate;
use Liberu\Accounting\SpreadsheetMigration\Actions\ValidateMigration;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationMode;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationStatus;
use Liberu\Accounting\SpreadsheetMigration\Exceptions\InvalidMigration;

uses(RefreshDatabase::class);
it('creates and validates a balanced detail migration', function (): void {
    $template = app(CreateMigrationTemplate::class)->handle(['name' => 'Invoices', 'entity' => 'invoice', 'mapping' => ['number' => 'A', 'amount' => 'B']]);
    $run = app(CreateMigrationRun::class)->handle($template, ['mode' => MigrationMode::Detail, 'rows' => [['number' => 'INV-1', 'amount' => 100]], 'source_total' => 100, 'target_total' => 100]);
    expect(app(ValidateMigration::class)->handle($run)->status)->toBe(MigrationStatus::Validated);
});
it('deduplicates identical imports and rejects unbalanced totals', function (): void {
    $template = app(CreateMigrationTemplate::class)->handle(['name' => 'Bills', 'entity' => 'bill', 'mapping' => ['number' => 'A']]);
    $data = ['rows' => [['number' => 'B-1']], 'source_total' => 100, 'target_total' => 90];
    $first = app(CreateMigrationRun::class)->handle($template, $data);
    $second = app(CreateMigrationRun::class)->handle($template, $data);
    expect($second->id)->toBe($first->id)->and(fn () => app(ValidateMigration::class)->handle($first))->toThrow(InvalidMigration::class);
});
