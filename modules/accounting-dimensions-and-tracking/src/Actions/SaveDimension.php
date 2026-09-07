<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Dimensions\Enums\DimensionKind;
use Liberu\Accounting\Dimensions\Exceptions\InvalidDimension;
use Liberu\Accounting\Dimensions\Models\Dimension;

final class SaveDimension
{
    public function handle(array $attributes, ?Dimension $dimension = null): Dimension
    {
        $kind = DimensionKind::tryFrom((string) ($attributes['kind'] ?? $dimension?->kind?->value));
        $attributes['kind'] = $kind?->value;
        if (blank($attributes['code'] ?? $dimension?->code) || blank($attributes['name'] ?? $dimension?->name) || $kind === null) {
            throw new InvalidDimension('A dimension requires a valid kind, code, and name.');
        }$attributes['code'] = $attributes['code'] ?? $dimension->code;
        $attributes['name'] = $attributes['name'] ?? $dimension->name;

        return DB::transaction(function () use ($attributes, $dimension) {
            $query = Dimension::query()->where('kind', $attributes['kind'])->where('code', $attributes['code']);
            if ($dimension) {
                $query->where($dimension->getKeyName(), '!=', $dimension->getKey());
            }if ($query->exists()) {
                throw new InvalidDimension('The dimension code is already in use for this kind.');
            }$dimension ??= new Dimension();
            $dimension->fill($attributes);
            $dimension->save();

            return $dimension->refresh();
        });
    }
}
