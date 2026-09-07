<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RecurringTransactions\Enums\RecurringStatus;
use Liberu\Accounting\RecurringTransactions\Exceptions\InvalidRecurringTransaction;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;

final class CreateRecurringTemplate
{
    public function handle(array $attributes): RecurringTemplate
    {
        $allowed = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];
        if (blank($attributes['name'] ?? null) || blank($attributes['transaction_type'] ?? null) || ! in_array($attributes['frequency'] ?? '', $allowed, true) || blank($attributes['starts_on'] ?? null)) {
            throw new InvalidRecurringTransaction('Name, transaction type, supported frequency, and starts_on are required.');
        }

        return DB::transaction(fn (): RecurringTemplate => RecurringTemplate::create(['team_id' => $attributes['team_id'] ?? null, 'name' => $attributes['name'], 'transaction_type' => $attributes['transaction_type'], 'frequency' => $attributes['frequency'], 'starts_on' => $attributes['starts_on'], 'next_run_on' => $attributes['next_run_on'] ?? $attributes['starts_on'], 'ends_on' => $attributes['ends_on'] ?? null, 'status' => RecurringStatus::Draft, 'automatic' => (bool) ($attributes['automatic'] ?? false), 'date_rules' => $attributes['date_rules'] ?? [], 'amount_rules' => $attributes['amount_rules'] ?? [], 'payload' => $attributes['payload'] ?? [], 'metadata' => $attributes['metadata'] ?? null]));
    }
}
