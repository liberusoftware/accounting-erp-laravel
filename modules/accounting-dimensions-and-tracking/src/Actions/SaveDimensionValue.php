<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Dimensions\Exceptions\InvalidDimension;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Liberu\Accounting\Dimensions\Models\DimensionValue;

final class SaveDimensionValue
{
    public function handle(Dimension $dimension, array $attributes, ?DimensionValue $value = null): DimensionValue
    {
        if (! $dimension->is_active) {
            throw new InvalidDimension('Cannot add a value to an inactive dimension.');
        }

        return DB::transaction(function () use ($dimension, $attributes, $value) {
            $query = $dimension->values()->where('code', $attributes['code'] ?? '');
            if ($value) {
                $query->where($value->getKeyName(), '!=', $value->getKey());
            }if ($query->exists()) {
                throw new InvalidDimension('The dimension value code is already in use.');
            }$value ??= new DimensionValue(['dimension_id' => $dimension->id]);
            $value->fill($attributes);
            $value->dimension_id = $dimension->id;
            $value->save();

            return $value->refresh();
        });
    }
}
