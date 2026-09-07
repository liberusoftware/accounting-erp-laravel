<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;
use Liberu\Accounting\AccountReconciliations\Exceptions\InvalidAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class CreateAccountReconciliation
{
    public function handle(array $attributes): AccountReconciliation
    {
        foreach (['team_id', 'book_id', 'account_id', 'period_start', 'period_end'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAccountReconciliation("{$field} is required.");
            }
        }

        if (($attributes['period_end'] ?? '') < ($attributes['period_start'] ?? '')) {
            throw new InvalidAccountReconciliation('The reconciliation period is invalid.');
        }

        return DB::transaction(fn (): AccountReconciliation => AccountReconciliation::create([
            ...$attributes,
            'status' => ReconciliationStatus::Draft,
            'supporting_items' => $attributes['supporting_items'] ?? [],
        ]));
    }
}
