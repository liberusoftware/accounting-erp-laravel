<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ProjectProfitability\Actions\FinalizeProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Actions\RecordProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Enums\ProfitabilityStatus;
use Liberu\Accounting\ProjectProfitability\Exceptions\InvalidProfitability;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Queries\ProjectProfitabilityDashboard;

uses(RefreshDatabase::class);
it('records idempotent periods and summarizes profitability', function (): void {
    $action = app(RecordProjectProfitability::class);
    $attributes = ['project_job_id' => 1, 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP', 'revenue_amount' => 1000, 'cost_amount' => 400, 'estimate_amount' => 1200, 'committed_amount' => 500, 'actual_amount' => 400, 'unbilled_wip_amount' => 100, 'billed_amount' => 900];
    $record = $action->handle($attributes);
    $same = $action->handle($attributes);
    expect($same->id)->toBe($record->id)->and(ProjectProfitability::count())->toBe(1);
    $summary = app(ProjectProfitabilityDashboard::class)->forProject(1);
    expect($summary['margin'])->toBe(600.0)->and($summary['realization_percent'])->toBe(75.0);
    expect(app(FinalizeProjectProfitability::class)->handle($record)->status)->toBe(ProfitabilityStatus::Final);
});
it('rejects invalid periods, negative values, and reversed finalization', function (): void {
    $action = app(RecordProjectProfitability::class);
    expect(fn () => $action->handle(['project_job_id' => 1, 'period_start' => '2026-02-01', 'period_end' => '2026-01-01']))->toThrow(InvalidProfitability::class);
    expect(fn () => $action->handle(['project_job_id' => 1, 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'cost_amount' => -1]))->toThrow(InvalidProfitability::class);
    $record = ProjectProfitability::create(['project_job_id' => 1, 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'status' => ProfitabilityStatus::Reversed]);
    expect(fn () => app(FinalizeProjectProfitability::class)->handle($record))->toThrow(InvalidProfitability::class);
});
