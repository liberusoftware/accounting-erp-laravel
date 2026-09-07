<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Actions;

use Liberu\Accounting\Depreciation\Enums\DepreciationMethod;
use Liberu\Accounting\Depreciation\Enums\DepreciationScheduleStatus;
use Liberu\Accounting\Depreciation\Exceptions\InvalidDepreciation;
use Liberu\Accounting\Depreciation\Models\DepreciationSchedule;

final class CreateDepreciationSchedule
{
    public function handle(array $attributes): DepreciationSchedule
    {
        $method = $attributes['method'] ?? null;
        $method = $method instanceof DepreciationMethod ? $method->value : $method;
        $cost = (float) ($attributes['cost'] ?? -1);
        $residual = (float) ($attributes['residual_value'] ?? 0);
        if (blank($attributes['team_id'] ?? null) || blank($attributes['asset_ref'] ?? null) || blank($attributes['book_ref'] ?? null) || ! in_array($method, array_column(DepreciationMethod::cases(), 'value'), true) || (int) ($attributes['useful_life_months'] ?? 0) < 1 || $cost < 0 || $residual < 0 || $residual > $cost || blank($attributes['start_date'] ?? null) || blank($attributes['currency'] ?? null)) {
            throw new InvalidDepreciation('A valid tenant, asset, book, method, useful life, value and start date are required.');
        }

        return DepreciationSchedule::create([...$attributes, 'method' => $method, 'status' => DepreciationScheduleStatus::Active, 'residual_value' => $residual]);
    }
}
