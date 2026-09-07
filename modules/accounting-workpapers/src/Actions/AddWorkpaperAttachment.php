<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Liberu\Accounting\Workpapers\Models\WorkpaperAttachment;

final class AddWorkpaperAttachment
{
    public function handle(Workpaper $workpaper, array $attributes): WorkpaperAttachment
    {
        if (blank($attributes['name'] ?? null) || blank($attributes['path'] ?? null)) {
            throw new InvalidWorkpaper('An attachment name and path are required.');
        }

        return $workpaper->attachments()->create(array_merge(['disk' => 'private'], $attributes));
    }
}
