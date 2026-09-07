<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OpeningBalances\Enums\BalanceType;
use Liberu\Accounting\OpeningBalances\Enums\EntryStatus;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;
use Liberu\Accounting\OpeningBalances\Exceptions\InvalidOpeningBalance;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

final class CreateOpeningBalanceBatch
{
    /** @param array<string,mixed> $attributes @param array<int,array<string,mixed>> $entries */
    public function handle(array $attributes, array $entries): OpeningBalanceBatch
    {
        foreach (['batch_ref', 'migration_date'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidOpeningBalance("Opening balance field [{$key}] is required.");
            }
        }if ($entries === []) {
            throw new InvalidOpeningBalance('An opening balance batch requires at least one entry.');
        }$hash = hash('sha256', json_encode(['attributes' => $attributes, 'entries' => $entries], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($attributes, $entries, $hash): OpeningBalanceBatch {
            $key = ['team_id' => $attributes['team_id'] ?? null, 'batch_ref' => $attributes['batch_ref']]; /** @var OpeningBalanceBatch|null $existing */
            $existing = OpeningBalanceBatch::query()->where($key)->first();
            if ($existing) {
                if ($existing->source_hash !== $hash) {
                    throw new InvalidOpeningBalance('Batch reference already exists with different source data.');
                }

                return $existing->load('entries');
            }$batch = OpeningBalanceBatch::create(array_merge($key, ['migration_date' => $attributes['migration_date'], 'currency' => isset($attributes['currency']) ? strtoupper((string) $attributes['currency']) : null, 'status' => OpeningBalanceStatus::Draft, 'source_hash' => $hash, 'idempotency_key' => $attributes['idempotency_key'] ?? null, 'requested_by' => $attributes['requested_by'] ?? null, 'metadata' => $attributes['metadata'] ?? null]));
            foreach ($entries as $entry) {
                $type = BalanceType::tryFrom((string) ($entry['balance_type'] ?? ''));
                if (! $type || blank($entry['reference_id'] ?? null)) {
                    throw new InvalidOpeningBalance('Each entry requires a supported balance type and reference_id.');
                }$debit = (float) ($entry['debit_amount'] ?? 0);
                $credit = (float) ($entry['credit_amount'] ?? 0);
                if ($debit < 0 || $credit < 0 || ($debit > 0 && $credit > 0) || ($debit === 0.0 && $credit === 0.0)) {
                    throw new InvalidOpeningBalance('Each entry must contain exactly one positive debit or credit amount.');
                }if (in_array($type, [BalanceType::Customer, BalanceType::Supplier], true) && blank($entry['document_ref'] ?? null)) {
                    throw new InvalidOpeningBalance('Customer and supplier entries require an outstanding document reference.');
                }$batch->entries()->create(['balance_type' => $type, 'reference_type' => $entry['reference_type'] ?? $type->value, 'reference_id' => (string) $entry['reference_id'], 'document_ref' => $entry['document_ref'] ?? null, 'document_date' => $entry['document_date'] ?? null, 'due_date' => $entry['due_date'] ?? null, 'currency' => strtoupper((string) ($entry['currency'] ?? $batch->currency)), 'debit_amount' => $debit, 'credit_amount' => $credit, 'status' => EntryStatus::Pending, 'description' => $entry['description'] ?? null, 'metadata' => $entry['metadata'] ?? null]);
            }$batch->audits()->create(['event_type' => 'opening_balances_created', 'actor_id' => $attributes['requested_by'] ?? null, 'payload' => ['entry_count' => count($entries)], 'payload_hash' => $hash, 'created_at' => now()]);

            return $batch->load('entries');
        });
    }
}
