<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ReceiptManagement\Enums\ReceiptStatus;
use Liberu\Accounting\ReceiptManagement\Exceptions\InvalidReceipt;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;
use Liberu\Accounting\ReceiptManagement\Models\ReceiptAudit;
use Liberu\Accounting\ReceiptManagement\Models\ReceiptMatch;

final class MatchReceipt
{
    public function handle(Receipt $receipt, array $attributes): ReceiptMatch
    {
        if ($receipt->status === ReceiptStatus::Purged) {
            throw new InvalidReceipt('A purged receipt cannot be matched.');
        }if (blank($attributes['target_type'] ?? null) || blank($attributes['target_id'] ?? null)) {
            throw new InvalidReceipt('A match target is required.');
        }

        return DB::transaction(function () use ($receipt, $attributes): ReceiptMatch {
            $match = ReceiptMatch::firstOrCreate(['receipt_id' => $receipt->id, 'target_type' => $attributes['target_type'], 'target_id' => (string) $attributes['target_id']], ['matched_amount' => $attributes['matched_amount'] ?? $receipt->amount, 'status' => 'confirmed', 'confidence' => $attributes['confidence'] ?? 1, 'metadata' => $attributes['metadata'] ?? null]);
            $receipt->update(['status' => ReceiptStatus::Matched]);
            ReceiptAudit::create(['receipt_id' => $receipt->id, 'action' => 'matched', 'actor_ref' => $attributes['actor_ref'] ?? null, 'evidence' => ['target_type' => $match->target_type, 'target_id' => $match->target_id], 'created_at' => now()]);

            return $match;
        });
    }
}
