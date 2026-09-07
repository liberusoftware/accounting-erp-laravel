<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Liberu\Accounting\Workpapers\Exceptions\InvalidWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Liberu\Accounting\Workpapers\Models\WorkpaperExport;

final class RequestWorkpaperExport
{
    public function handle(Workpaper $workpaper, string $format): WorkpaperExport
    {
        if (! in_array($format, ['csv', 'json', 'pdf'], true)) {
            throw new InvalidWorkpaper('The export format must be csv, json, or pdf.');
        }

        return $workpaper->exports()->create(['format' => $format, 'status' => 'pending']);
    }
}
