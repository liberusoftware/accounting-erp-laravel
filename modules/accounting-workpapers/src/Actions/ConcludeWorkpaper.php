<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Workpapers\Enums\WorkpaperStatus;
use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;

final class ConcludeWorkpaper
{
    public function handle(Workpaper $workpaper, string $conclusion): Workpaper
    {
        if (blank($conclusion)) {
            throw new InvalidWorkpaper('A conclusion is required.');
        }

        return DB::transaction(function () use ($workpaper, $conclusion): Workpaper {
            $workpaper->update(['conclusion' => $conclusion, 'status' => WorkpaperStatus::Complete]);

            return $workpaper->fresh();
        });
    }
}
