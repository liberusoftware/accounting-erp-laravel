<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperAttachment;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperProcedure;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperReference;
use Liberu\Accounting\Workpapers\Actions\ConcludeWorkpaper;
use Liberu\Accounting\Workpapers\Actions\CreateWorkpaper;
use Liberu\Accounting\Workpapers\Actions\RolloverWorkpaper;
use Liberu\Accounting\Workpapers\Enums\ProcedureStatus;
use Liberu\Accounting\Workpapers\Enums\WorkpaperStatus;
use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;

uses(RefreshDatabase::class);

it('supports procedures, conclusions and tenant-safe rollover', function (): void {
    $workpaper = app(CreateWorkpaper::class)->handle(['team_id' => 42, 'title' => 'Bank reconciliation', 'reference' => 'WP-001', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31']);
    $procedure = app(AddWorkpaperProcedure::class)->handle($workpaper, ['description' => 'Agree the bank statement to the ledger.', 'status' => ProcedureStatus::Passed]);
    app(AddWorkpaperReference::class)->handle($workpaper, ['label' => 'Bank statement January', 'target_type' => 'document', 'target_id' => 'statement-1']);
    app(AddWorkpaperAttachment::class)->handle($workpaper, ['name' => 'statement.pdf', 'path' => 'workpapers/statement.pdf']);
    $copy = app(RolloverWorkpaper::class)->handle($workpaper, ['title' => 'Bank reconciliation - February', 'period_start' => '2026-02-01', 'period_end' => '2026-02-28']);
    $closed = app(ConcludeWorkpaper::class)->handle($workpaper, 'No unresolved differences identified.');

    expect($procedure->status)->toBe(ProcedureStatus::Passed)
        ->and($closed->status)->toBe(WorkpaperStatus::Complete)
        ->and($copy->team_id)->toBe(42)
        ->and($copy->references()->count())->toBe(1)
        ->and($workpaper->attachments()->count())->toBe(1);
});

it('rejects invalid workpaper input', function (): void {
    expect(fn (): mixed => app(CreateWorkpaper::class)->handle(['team_id' => 42, 'title' => 'Test', 'period_start' => '2026-02-01', 'period_end' => '2026-01-01']))
        ->toThrow(InvalidWorkpaper::class);
});
