<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies\Queries;

use Liberu\Accounting\Policies\Enums\PolicyCategory;
use Liberu\Accounting\Policies\Models\PolicyRule;

final class PolicyRuleForDate
{
    public function handle(int|string $bookId, PolicyCategory|string $category, string $key, \DateTimeInterface|string $date): ?PolicyRule
    {
        $category = $category instanceof PolicyCategory ? $category->value : $category;
        $date = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;

        return PolicyRule::query()->where('book_id', $bookId)->where('category', $category)->where('key', $key)->where('is_active', true)->where('effective_from', '<=', $date)->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $date))->latest('effective_from')->first();
    }
}
