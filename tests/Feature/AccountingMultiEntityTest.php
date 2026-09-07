<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityBook;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityMapping;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityPeriod;
use Liberu\Accounting\MultiEntity\Actions\GrantEntityAccess;
use Liberu\Accounting\MultiEntity\Actions\SetMasterDataPolicy;
use Liberu\Accounting\MultiEntity\Actions\SwitchEntity;
use Liberu\Accounting\MultiEntity\Enums\EntityPeriodStatus;
use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Queries\EntityQuery;

uses(RefreshDatabase::class);

it('supports entity books, access, switching, policies, periods, mappings, and reporting', function (): void {
    $entity = app(CreateEntityBook::class)->handle(['team_id' => 1, 'entity_ref' => 'entity-uk', 'code' => 'UK01', 'name' => 'UK Holdings', 'base_currency' => 'GBP']);
    app(GrantEntityAccess::class)->handle($entity, 'user-1', 'controller', ['report.read'], true);
    $policy = app(SetMasterDataPolicy::class)->handle($entity, 'customers', 'shared', ['owner' => 'group']);
    $period = app(CreateEntityPeriod::class)->handle($entity, ['period_ref' => '2026-Q1', 'starts_on' => '2026-01-01', 'ends_on' => '2026-03-31', 'tax_configuration' => ['rate' => 20]]);
    $mapping = app(CreateEntityMapping::class)->handle($entity, 'chart-of-accounts', '4000', 'REV-UK');
    $switch = app(SwitchEntity::class)->handle($entity, 'user-1', 'session-1');

    expect($policy->mode)->toBe('shared')->and($period->status)->toBe(EntityPeriodStatus::Open)->and($mapping->target_ref)->toBe('REV-UK')->and($switch->session_ref)->toBe('session-1')->and(app(EntityQuery::class)->report($entity->refresh())['open_periods'])->toBe(1);
    expect(fn (): mixed => app(SwitchEntity::class)->handle($entity, 'user-2', 'session-2'))->toThrow(InvalidEntity::class);
    expect(fn (): mixed => app(CreateEntityPeriod::class)->handle($entity, ['period_ref' => 'overlap', 'starts_on' => '2026-02-01', 'ends_on' => '2026-02-15']))->toThrow(InvalidEntity::class);
});

it('is tenant scoped and exposes the authenticated entity API', function (): void {
    app(CreateEntityBook::class)->handle(['team_id' => 1, 'entity_ref' => 'entity-uk', 'code' => 'UK01', 'name' => 'UK Holdings', 'base_currency' => 'GBP']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.multi-entity.read', 'accounting.multi-entity.write']);
    $this->getJson('/api/v1/accounting/multi-entity')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/multi-entity/1/report')->assertOk()->assertJsonPath('entity_ref', 'entity-uk');
    expect(app(EntityQuery::class)->paginate(2)->total())->toBe(0);
});
