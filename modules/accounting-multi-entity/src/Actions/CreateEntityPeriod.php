<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Liberu\Accounting\MultiEntity\Enums\EntityPeriodStatus;
use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;
use Liberu\Accounting\MultiEntity\Models\EntityPeriod;

final class CreateEntityPeriod
{
    public function handle(EntityBook $entity, array $attributes): EntityPeriod
    {
        if (blank($attributes['period_ref'] ?? null) || blank($attributes['starts_on'] ?? null) || blank($attributes['ends_on'] ?? null) || $attributes['ends_on'] < $attributes['starts_on']) {
            throw new InvalidEntity('Period reference and an ordered date range are required.');
        }if ($entity->periods()->where('starts_on', '<=', $attributes['ends_on'])->where('ends_on', '>=', $attributes['starts_on'])->exists()) {
            throw new InvalidEntity('The period overlaps an existing entity period.');
        }/** @var EntityPeriod $period */ $period = $entity->periods()->create(['period_ref' => $attributes['period_ref'], 'starts_on' => $attributes['starts_on'], 'ends_on' => $attributes['ends_on'], 'tax_configuration' => $attributes['tax_configuration'] ?? null, 'status' => EntityPeriodStatus::Open, 'metadata' => $attributes['metadata'] ?? null]);

        return $period;
    }
}
