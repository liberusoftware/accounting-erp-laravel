<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Depreciation\Actions\CreateDepreciationSchedule;
use Liberu\Accounting\Depreciation\Actions\PostDepreciationRun;
use Liberu\Accounting\Depreciation\Actions\RunDepreciation;
use Liberu\Accounting\Depreciation\Enums\DepreciationMethod;
use Liberu\Accounting\Depreciation\Enums\DepreciationRunStatus;
use Liberu\Accounting\Depreciation\Enums\DepreciationScheduleStatus;
use Liberu\Accounting\Depreciation\Exceptions\InvalidDepreciation;
use Liberu\Accounting\Depreciation\Queries\DepreciationForecast;

uses(RefreshDatabase::class);

it('creates, runs, posts and forecasts a straight-line schedule', function (): void {
    $schedule = app(CreateDepreciationSchedule::class)->handle([
        'team_id' => 17,
        'asset_ref' => 'asset-17',
        'book_ref' => 'statutory',
        'method' => DepreciationMethod::StraightLine,
        'useful_life_months' => 12,
        'cost' => 1200,
        'residual_value' => 0,
        'start_date' => '2026-01-01',
        'currency' => 'GBP',
    ]);
    $run = app(RunDepreciation::class)->handle($schedule, '2026-01-01', '2026-01-31');

    expect($schedule->status)->toBe(DepreciationScheduleStatus::Active)
        ->and((float) $run->amount)->toBe(100.0)
        ->and($run->status)->toBe(DepreciationRunStatus::Calculated);

    $posted = app(PostDepreciationRun::class)->handle($run, 9, 'JRN-DEP-1');
    expect($posted->status)->toBe(DepreciationRunStatus::Posted)
        ->and(app(DepreciationForecast::class)->forTeam(17)->first()['remaining'])->toBe(1100.0);
});

it('rejects invalid schedules and duplicate periods', function (): void {
    expect(fn () => app(CreateDepreciationSchedule::class)->handle([
        'team_id' => 17,
        'asset_ref' => 'asset-invalid',
        'book_ref' => 'tax',
        'method' => 'straight_line',
        'useful_life_months' => 12,
        'cost' => 100,
        'residual_value' => 101,
        'start_date' => '2026-01-01',
        'currency' => 'GBP',
    ]))->toThrow(InvalidDepreciation::class);

    $schedule = app(CreateDepreciationSchedule::class)->handle([
        'team_id' => 17, 'asset_ref' => 'asset-duplicate', 'book_ref' => 'statutory', 'method' => 'straight_line', 'useful_life_months' => 12, 'cost' => 1200, 'start_date' => '2026-01-01', 'currency' => 'GBP',
    ]);
    app(RunDepreciation::class)->handle($schedule, '2026-01-01', '2026-01-31');

    expect(fn () => app(RunDepreciation::class)->handle($schedule, '2026-01-01', '2026-01-31'))->toThrow(InvalidDepreciation::class);
});
