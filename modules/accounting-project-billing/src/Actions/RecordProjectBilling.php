<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectBilling\Enums\BillingMethod;
use Liberu\Accounting\ProjectBilling\Enums\BillingStatus;
use Liberu\Accounting\ProjectBilling\Exceptions\InvalidBilling;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;

final class RecordProjectBilling
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): ProjectBilling
    {
        $method = BillingMethod::from((string) $attributes['method']);
        $start = CarbonImmutable::parse((string) ($attributes['period_start'] ?? now()->startOfMonth()->toDateString()));
        $end = CarbonImmutable::parse((string) ($attributes['period_end'] ?? now()->toDateString()));
        if ($end->lessThan($start)) {
            throw new InvalidBilling('The billing period must be ordered.');
        }$progress = (float) ($attributes['progress_percent'] ?? 0);
        if ($progress < 0 || $progress > 100) {
            throw new InvalidBilling('Progress must be between 0 and 100 percent.');
        }foreach (['quantity', 'rate', 'amount', 'billable_time_amount', 'billable_expense_amount', 'retainer_amount'] as $field) {
            if (isset($attributes[$field]) && (float) $attributes[$field] < 0) {
                throw new InvalidBilling($field.' cannot be negative.');
            }
        }

        return DB::transaction(function () use ($attributes, $method, $start, $end): ProjectBilling {
            $query = ProjectBilling::query()->where('project_job_id', $attributes['project_job_id'])->where('method', $method->value)->where('source_ref', $attributes['source_ref'] ?? null)->whereDate('period_start', $start)->whereDate('period_end', $end)->when(array_key_exists('team_id', $attributes), fn ($query) => $query->where('team_id', $attributes['team_id']));
            $record = $query->first() ?? new ProjectBilling();
            $record->fill(array_merge($attributes, ['method' => $method, 'status' => $attributes['status'] ?? BillingStatus::Draft, 'period_start' => $start, 'period_end' => $end]));
            $record->save();

            return $record;
        });
    }
}
