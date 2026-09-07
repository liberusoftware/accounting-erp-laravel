<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class TaxRuleQuery
{
    public function paginate(?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return TaxRule::query()->when($status, fn ($query) => $query->where('status', $status))->orderBy('code')->paginate(min(max($perPage, 1), 100));
    }
}
