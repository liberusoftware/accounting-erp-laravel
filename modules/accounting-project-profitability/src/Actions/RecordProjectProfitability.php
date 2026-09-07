<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectProfitability\Enums\ProfitabilityStatus;
use Liberu\Accounting\ProjectProfitability\Exceptions\InvalidProfitability;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;

final class RecordProjectProfitability
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): ProjectProfitability
    {
        $start = CarbonImmutable::parse((string) $attributes['period_start']);
        $end = CarbonImmutable::parse((string) $attributes['period_end']);
        if ($end->lessThan($start)) {
            throw new InvalidProfitability('The profitability period must end on or after it starts.');
        }
        foreach (['revenue_amount', 'cost_amount', 'estimate_amount', 'committed_amount', 'actual_amount', 'unbilled_wip_amount', 'billed_amount'] as $field) {
            if (isset($attributes[$field]) && (float) $attributes[$field] < 0) {
                throw new InvalidProfitability($field.' cannot be negative.');
            }
        }

        return DB::transaction(function () use ($attributes, $start, $end): ProjectProfitability {
            $record = ProjectProfitability::query()
                ->where('project_job_id', $attributes['project_job_id'])
                ->whereDate('period_start', $start)
                ->whereDate('period_end', $end)
                ->when(array_key_exists('team_id', $attributes), fn ($query) => $query->where('team_id', $attributes['team_id']))
                ->first() ?? new ProjectProfitability();
            $record->fill(array_merge($attributes, ['period_start' => $start, 'period_end' => $end, 'status' => $attributes['status'] ?? ProfitabilityStatus::Draft]));
            $record->save();

            return $record;
        });
    }
}
