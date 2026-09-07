<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

final class CreatePeriod
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): AccountingPeriod
    {
        return DB::transaction(function () use ($attributes): AccountingPeriod {
            if ($attributes['starts_on'] > $attributes['ends_on']) {
                throw new \InvalidArgumentException('Period start must not be after its end.');
            }
            if (isset($attributes['posting_ends_on']) && $attributes['posting_ends_on'] > $attributes['ends_on']) {
                throw new \InvalidArgumentException('Posting end must not be after the period end.');
            }
            $overlap = AccountingPeriod::query()->where('book_id', $attributes['book_id'])->where('starts_on', '<=', $attributes['ends_on'])->where('ends_on', '>=', $attributes['starts_on'])->exists();
            if ($overlap) {
                throw new \InvalidArgumentException('Accounting periods may not overlap within a book.');
            }

            return AccountingPeriod::query()->create($attributes + ['state' => 'open']);
        });
    }
}
