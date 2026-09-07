<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Consolidation\Actions\AddConsolidationEntity;
use Liberu\Accounting\Consolidation\Actions\CreateConsolidationGroup;
use Liberu\Accounting\Consolidation\Actions\PrepareConsolidatedReport;
use Liberu\Accounting\Consolidation\Actions\PublishConsolidatedReport;
use Liberu\Accounting\Consolidation\Actions\RecordEliminations;
use Liberu\Accounting\Consolidation\Enums\ConsolidationStatus;
use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;

uses(RefreshDatabase::class);

it('builds, eliminates and publishes a consolidated report', function (): void {
    $group = app(CreateConsolidationGroup::class)->handle(['team_id' => 404, 'group_ref' => 'group-1', 'name' => 'Group One', 'reporting_currency' => 'GBP']);
    app(AddConsolidationEntity::class)->handle($group, ['entity_ref' => 'entity-1', 'ownership_percent' => 100]);
    app(RecordEliminations::class)->handle($group->refresh(), ['reference' => 'elim-1', 'amount' => 250]);
    app(PrepareConsolidatedReport::class)->handle($group->refresh(), ['period' => '2026-Q3']);
    $published = app(PublishConsolidatedReport::class)->handle($group->refresh(), ['method' => 'closing-rate']);

    expect($published->status)->toBe(ConsolidationStatus::Reported)
        ->and($published->entities)->toHaveCount(1)
        ->and($published->eliminations)->toHaveCount(1);
});

it('rejects reports without entities', function (): void {
    $group = app(CreateConsolidationGroup::class)->handle(['team_id' => 404, 'group_ref' => 'group-2', 'name' => 'Group Two', 'reporting_currency' => 'USD']);

    expect(fn () => app(PrepareConsolidatedReport::class)->handle($group, ['period' => '2026-Q3']))
        ->toThrow(InvalidConsolidation::class);
});
