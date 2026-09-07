<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TaxReturns\Enums\TaxReturnStatus;
use Liberu\Accounting\TaxReturns\Exceptions\InvalidTaxReturn;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;

final class CreateTaxReturn
{
    public function handle(array $attributes): TaxReturn
    {
        if (blank($attributes['tax_type'] ?? null) || blank($attributes['jurisdiction'] ?? null) || blank($attributes['period_start'] ?? null) || blank($attributes['period_end'] ?? null) || $attributes['period_end'] < $attributes['period_start']) {
            throw new InvalidTaxReturn('Tax type, jurisdiction, and a valid return period are required.');
        }

        return DB::transaction(fn (): TaxReturn => TaxReturn::create(array_merge($attributes, ['status' => $attributes['status'] ?? TaxReturnStatus::Draft])));
    }
}
