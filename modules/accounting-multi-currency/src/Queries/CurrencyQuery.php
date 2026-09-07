<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\MultiCurrency\Models\ExchangeRate;
use Liberu\Accounting\MultiCurrency\Models\RevaluationRun;

final class CurrencyQuery
{
    public function rates(?int $teamId = null, ?string $from = null, ?string $to = null, int $perPage = 25): LengthAwarePaginator
    {
        return ExchangeRate::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($from, fn ($q) => $q->where('from_currency', $from))->when($to, fn ($q) => $q->where('to_currency', $to))->latest('rate_date')->paginate(min(max($perPage, 1), 100));
    }

    public function revaluations(?int $teamId = null): LengthAwarePaginator
    {
        return RevaluationRun::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->latest('as_of_date')->paginate(25);
    }

    public function report(RevaluationRun $run): array
    {
        return ['run_ref' => $run->run_ref, 'as_of_date' => $run->as_of_date?->toDateString(), 'functional_currency' => $run->functional_currency, 'status' => $run->status->value, 'summary' => $run->summary];
    }
}
