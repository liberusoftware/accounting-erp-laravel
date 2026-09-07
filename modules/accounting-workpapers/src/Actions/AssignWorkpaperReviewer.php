<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Liberu\Accounting\Workpapers\Models\Workpaper;

final class AssignWorkpaperReviewer
{
    public function handle(Workpaper $workpaper, int $reviewerId): Workpaper
    {
        $workpaper->update(['reviewer_id' => $reviewerId]);

        return $workpaper->fresh();
    }
}
