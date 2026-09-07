<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Actions;

use Liberu\Accounting\Review\Enums\ReviewStatus;
use Liberu\Accounting\Review\Exceptions\InvalidReviewItem;
use Liberu\Accounting\Review\Models\ReviewItem;

final class ResolveReviewItem
{
    public function handle(ReviewItem $item, int $actorId, array $resolution): ReviewItem
    {
        if (! in_array($item->status, [ReviewStatus::Open, ReviewStatus::InProgress], true) || blank($resolution['summary'] ?? null)) {
            throw new InvalidReviewItem('Only open review items with a resolution summary can be resolved.');
        }
        $item->update(['status' => ReviewStatus::Resolved, 'resolution' => [...$resolution, 'resolved_by' => $actorId], 'resolved_by' => $actorId, 'resolved_at' => now()]);

        return $item;
    }
}
