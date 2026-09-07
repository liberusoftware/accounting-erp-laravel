<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Workpapers\Enums\WorkpaperStatus;
use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;

final class CreateWorkpaper
{
    public function handle(array $attributes): Workpaper
    {
        if (blank($attributes['title'] ?? null) || blank($attributes['team_id'] ?? null)) {
            throw new InvalidWorkpaper('A team and workpaper title are required.');
        }

        if (isset($attributes['period_start'], $attributes['period_end']) && $attributes['period_start'] > $attributes['period_end']) {
            throw new InvalidWorkpaper('The workpaper period must be chronological.');
        }

        return DB::transaction(fn (): Workpaper => Workpaper::create(array_merge($attributes, ['status' => $attributes['status'] ?? WorkpaperStatus::Draft])));
    }
}
