<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\KpiAndGoals\Exceptions\InvalidKpi;
use Liberu\Accounting\KpiAndGoals\Models\KpiMetric;

final class CreateMetric
{
    public function handle(array $attributes): KpiMetric
    {
        $ref = trim((string) ($attributes['metric_ref'] ?? ''));
        if ($ref === '' || blank($attributes['name'] ?? null) || blank($attributes['unit'] ?? null) || blank($attributes['source_contract'] ?? null)) {
            throw new InvalidKpi('Metric requires reference, name, unit, and source contract.');
        }if (! in_array($attributes['direction'] ?? 'higher', ['higher', 'lower'], true)) {
            throw new InvalidKpi('Metric direction must be higher or lower.');
        }

        return DB::transaction(fn (): KpiMetric => KpiMetric::create(['team_id' => $attributes['team_id'] ?? null, 'metric_ref' => $ref, 'name' => $attributes['name'], 'description' => $attributes['description'] ?? null, 'unit' => $attributes['unit'], 'direction' => $attributes['direction'] ?? 'higher', 'source_contract' => $attributes['source_contract'], 'formula' => $attributes['formula'] ?? null, 'owner_ref' => $attributes['owner_ref'] ?? null, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
