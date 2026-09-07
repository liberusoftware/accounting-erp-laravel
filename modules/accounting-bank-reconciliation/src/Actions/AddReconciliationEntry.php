<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationEntryKind;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationEntryStatus;
use Liberu\Accounting\BankReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationEntry;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

final class AddReconciliationEntry
{
    public function handle(ReconciliationSession $session, array $attributes): ReconciliationEntry
    {
        if ($session->status->value === 'signed_off' || ! in_array((string) ($attributes['kind'] ?? ''), array_column(ReconciliationEntryKind::cases(), 'value'), true) || ! is_numeric($attributes['amount'] ?? null) || blank($attributes['currency'] ?? null)) {
            throw new InvalidReconciliation('Signed-off sessions cannot change and entries require a supported kind, amount, and currency.');
        }

        return DB::transaction(function () use ($session, $attributes): ReconciliationEntry {
            $sourceType = $attributes['source_type'] ?? null;
            $sourceId = $attributes['source_id'] ?? null;
            if ($sourceType !== null && $sourceId !== null && ReconciliationEntry::query()->where('session_id', $session->getKey())->where('source_type', $sourceType)->where('source_id', $sourceId)->exists()) {
                throw new InvalidReconciliation('This source is already represented in the reconciliation.');
            }

            return $session->entries()->create(array_merge($attributes, ['team_id' => $session->team_id, 'currency' => strtoupper((string) $attributes['currency']), 'status' => $attributes['status'] ?? ReconciliationEntryStatus::Suggested]));
        });
    }
}
