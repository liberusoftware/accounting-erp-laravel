<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;

final class FindAccountingItems
{
    /** @return Builder<AccountingItem> */
    public function search(?string $term = null): Builder
    {
        $query = AccountingItem::query()->where('status', 'active');
        if ($term !== null && trim($term) !== '') {
            $query->where(fn (Builder $q): Builder => $q->where('code', 'like', '%'.trim($term).'%')->orWhere('name', 'like', '%'.trim($term).'%'));
        }

        return $query->orderBy('code');
    }
}
