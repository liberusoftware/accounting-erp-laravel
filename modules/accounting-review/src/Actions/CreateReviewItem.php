<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Review\Enums\ReviewStatus;
use Liberu\Accounting\Review\Exceptions\InvalidReviewItem;
use Liberu\Accounting\Review\Models\ReviewItem;

final class CreateReviewItem
{
    public function handle(array $attributes): ReviewItem
    {
        foreach (['team_id', 'item_type', 'title'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidReviewItem("{$field} is required.");
            }
        }

        return DB::transaction(fn (): ReviewItem => ReviewItem::create([...$attributes, 'status' => ReviewStatus::Open, 'severity' => $attributes['severity'] ?? 'medium']));
    }
}
