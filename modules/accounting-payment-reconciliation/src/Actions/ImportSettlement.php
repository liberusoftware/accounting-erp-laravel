<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemType;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementStatus;
use Liberu\Accounting\PaymentReconciliation\Events\SettlementImported;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class ImportSettlement
{
    /** @param array<string,mixed> $attributes @param array<int,array<string,mixed>> $items */
    public function handle(array $attributes, array $items): SettlementRun
    {
        foreach (['provider', 'settlement_ref', 'currency', 'period_start', 'period_end'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidReconciliation("Settlement field [{$key}] is required.");
            }
        }
        if ($items === []) {
            throw new InvalidReconciliation('A settlement must contain at least one item.');
        }
        $currency = strtoupper((string) $attributes['currency']);
        if (! preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidReconciliation('Currency must be an ISO 4217 code.');
        }
        $sourceHash = hash('sha256', json_encode(['attributes' => $attributes, 'items' => $items], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($attributes, $items, $currency, $sourceHash): SettlementRun {
            $key = ['team_id' => $attributes['team_id'] ?? null, 'provider' => $attributes['provider'], 'settlement_ref' => $attributes['settlement_ref']];
            $existing = SettlementRun::query()->where($key)->first();
            if ($existing) {
                if ($existing->source_hash !== $sourceHash) {
                    throw new InvalidReconciliation('Settlement reference already exists with different source data.');
                }

                return $existing->load('items');
            }
            $totals = ['gross' => 0.0, 'fee' => 0.0, 'refund' => 0.0, 'dispute' => 0.0, 'net' => 0.0];
            $run = SettlementRun::create(array_merge($key, ['merchant_ref' => $attributes['merchant_ref'] ?? null, 'period_start' => $attributes['period_start'], 'period_end' => $attributes['period_end'], 'currency' => $currency, 'status' => SettlementStatus::Imported, 'idempotency_key' => $attributes['idempotency_key'] ?? null, 'source_hash' => $sourceHash, 'metadata' => $attributes['metadata'] ?? null]));
            foreach ($items as $item) {
                $ref = (string) ($item['external_ref'] ?? '');
                $type = SettlementItemType::tryFrom((string) ($item['type'] ?? ''));
                if ($ref === '' || ! $type) {
                    throw new InvalidReconciliation('Every settlement item requires a unique external reference and supported type.');
                }$itemCurrency = strtoupper((string) ($item['currency'] ?? $currency));
                if ($itemCurrency !== $currency) {
                    throw new InvalidReconciliation('Settlement items must use the run currency.');
                }foreach (['gross_amount', 'fee_amount', 'refund_amount', 'dispute_amount', 'net_amount'] as $amount) {
                    if ((float) ($item[$amount] ?? 0) < 0) {
                        throw new InvalidReconciliation("Item amount [{$amount}] cannot be negative.");
                    }
                }$run->items()->create(['external_ref' => $ref, 'type' => $type, 'currency' => $currency, 'gross_amount' => $item['gross_amount'] ?? 0, 'fee_amount' => $item['fee_amount'] ?? 0, 'refund_amount' => $item['refund_amount'] ?? 0, 'dispute_amount' => $item['dispute_amount'] ?? 0, 'net_amount' => $item['net_amount'] ?? 0, 'status' => SettlementItemStatus::Unmatched, 'source_payload' => $item['source_payload'] ?? $item, 'source_hash' => hash('sha256', json_encode($item, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)), 'metadata' => $item['metadata'] ?? null]);
                foreach (array_keys($totals) as $part) {
                    $totals[$part] += round((float) ($item[$part.'_amount'] ?? 0), 2);
                }
            }
            $run->update(['gross_amount' => $totals['gross'], 'fee_amount' => $totals['fee'], 'refund_amount' => $totals['refund'], 'dispute_amount' => $totals['dispute'], 'net_amount' => $totals['net']]);
            $run->audits()->create(['event_type' => 'settlement_imported', 'actor_id' => $attributes['actor_id'] ?? null, 'payload' => ['item_count' => count($items), 'source_hash' => $sourceHash], 'payload_hash' => hash('sha256', json_encode($attributes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)), 'created_at' => now()]);
            $run = $run->refresh()->load('items');
            DB::afterCommit(fn () => event(new SettlementImported($run)));

            return $run;
        });
    }
}
