<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Actions;

use Liberu\Accounting\Review\Enums\ReviewStatus;
use Liberu\Accounting\Review\Exceptions\InvalidReviewItem;
use Liberu\Accounting\Review\Models\ReviewItem;

final class SignOffReviewItem
{
    public function handle(ReviewItem $item, int $actorId, string $attestation): ReviewItem
    {
        if ($item->status !== ReviewStatus::Resolved || blank($attestation)) {
            throw new InvalidReviewItem('Only resolved review items can be signed off.');
        }
        $item->update(['status' => ReviewStatus::SignedOff, 'signoff' => ['attestation' => $attestation, 'signed_off_by' => $actorId], 'signed_off_by' => $actorId, 'signed_off_at' => now()]);

        return $item;
    }
}
