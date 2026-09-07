<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementStatus;
use Liberu\Accounting\PaymentReconciliation\Events\SettlementMatched;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementItem;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementMatch;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class MatchSettlementItem
{
    public function handle(SettlementItem $item, string $referenceType, string $referenceId, float $amount, ?int $actorId = null, ?string $idempotencyKey = null): SettlementMatch
    {
        if (blank($referenceType) || blank($referenceId) || $amount <= 0) {
            throw new InvalidReconciliation('A match requires a reference and a positive amount.');
        }

        return DB::transaction(function () use ($item, $referenceType, $referenceId, $amount, $actorId, $idempotencyKey): SettlementMatch {
            $item = SettlementItem::query()->lockForUpdate()->findOrFail($item->id);
            $existing = SettlementMatch::query()->where('item_id', $item->id)->where('reference_type', $referenceType)->where('reference_id', $referenceId)->first();
            if ($existing) {
                return $existing;
            }if ($amount > (float) $item->net_amount + 0.01) {
                throw new InvalidReconciliation('Matched amount cannot exceed the settlement item net amount.');
            }$matched = (float) SettlementMatch::query()->where('item_id', $item->id)->sum('matched_amount');
            if ($matched + $amount > (float) $item->net_amount + 0.01) {
                throw new InvalidReconciliation('Total matched amount cannot exceed the settlement item net amount.');
            }$match = SettlementMatch::create(['run_id' => $item->run_id, 'item_id' => $item->id, 'reference_type' => $referenceType, 'reference_id' => $referenceId, 'matched_amount' => round($amount, 2), 'confidence' => min(1, max(0, $amount / (float) $item->net_amount)), 'status' => $matched + $amount + 0.01 >= (float) $item->net_amount ? 'matched' : 'partial', 'matched_by' => $actorId, 'matched_at' => now(), 'idempotency_key' => $idempotencyKey]);
            $item->update(['status' => $matched + $amount + 0.01 >= (float) $item->net_amount ? SettlementItemStatus::Matched : SettlementItemStatus::PartiallyMatched, 'reference_type' => $referenceType, 'reference_id' => $referenceId]); /** @var SettlementRun $run */
            $run = $item->run()->lockForUpdate()->firstOrFail();
            $open = $run->items()->whereIn('status', [SettlementItemStatus::Unmatched->value, SettlementItemStatus::PartiallyMatched->value])->exists();
            $run->update(['status' => $open ? SettlementStatus::PartiallyMatched : SettlementStatus::Matched]);
            $run->audits()->create(['event_type' => 'settlement_item_matched', 'actor_id' => $actorId, 'payload' => ['item_id' => $item->id, 'match_id' => $match->id, 'reference_type' => $referenceType, 'reference_id' => $referenceId, 'amount' => $amount], 'payload_hash' => hash('sha256', $referenceType.':'.$referenceId.':'.$amount), 'created_at' => now()]);
            DB::afterCommit(fn () => event(new SettlementMatched($match->refresh())));

            return $match->refresh();
        });
    }
}
