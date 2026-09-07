<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ProjectCosting\Actions\RecordProjectCost;
use Liberu\Accounting\ProjectCosting\Enums\CostType;
use Liberu\Accounting\ProjectCosting\Exceptions\InvalidCost;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;
use Liberu\Accounting\ProjectCosting\Queries\ProjectCostSummary;

uses(RefreshDatabase::class);
it('records idempotent costs and calculates committed actual WIP and variance', function (): void {
    $action = app(RecordProjectCost::class);
    $base = ['project_job_id' => 1, 'type' => 'labor', 'occurred_on' => '2026-01-01', 'amount' => 100, 'currency' => 'GBP', 'source_ref' => 'LAB-1', 'committed' => true, 'actual' => true, 'wip_amount' => 20];
    $first = $action->handle($base);
    $second = $action->handle(array_merge($base, ['amount' => 125]));
    expect($first->id)->toBe($second->id)->and(ProjectCost::count())->toBe(1);
    $action->handle(array_merge($base, ['type' => 'expense', 'source_ref' => 'EXP-1', 'amount' => 75, 'committed' => false, 'actual' => true]));
    $summary = app(ProjectCostSummary::class)->forProject(1);
    expect($summary['total_cost'])->toBe(200.0)->and($summary['actual'])->toBe(200.0)->and($summary['by_type']['labor'])->toBe(125.0);
});
it('rejects negative cost values and unsupported types', function (): void {
    $action = app(RecordProjectCost::class);
    expect(fn () => $action->handle(['project_job_id' => 1, 'type' => 'labor', 'amount' => -1]))->toThrow(InvalidCost::class);
    expect(fn () => $action->handle(['project_job_id' => 1, 'type' => 'other', 'amount' => 1]))->toThrow(ValueError::class);
    expect(CostType::Labor->value)->toBe('labor');
});
