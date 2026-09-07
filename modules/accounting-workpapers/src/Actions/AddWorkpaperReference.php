<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Liberu\Accounting\Workpapers\Models\WorkpaperReference;

final class AddWorkpaperReference
{
    public function handle(Workpaper $workpaper, array $attributes): WorkpaperReference
    {
        if (blank($attributes['label'] ?? null)) {
            throw new InvalidWorkpaper('A reference label is required.');
        }

        return $workpaper->references()->create($attributes);
    }
}
