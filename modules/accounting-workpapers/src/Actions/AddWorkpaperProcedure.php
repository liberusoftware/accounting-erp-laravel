<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Workpapers\Enums\ProcedureStatus;
use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Liberu\Accounting\Workpapers\Models\WorkpaperProcedure;

final class AddWorkpaperProcedure
{
    public function handle(Workpaper $workpaper, array $attributes): WorkpaperProcedure
    {
        if (blank($attributes['description'] ?? null)) {
            throw new InvalidWorkpaper('A procedure description is required.');
        }

        return DB::transaction(fn (): WorkpaperProcedure => $workpaper->procedures()->create(array_merge($attributes, ['status' => $attributes['status'] ?? ProcedureStatus::Pending])));
    }
}
