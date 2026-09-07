<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ReceiptManagement\Enums\ReceiptStatus;
use Liberu\Accounting\ReceiptManagement\Exceptions\InvalidReceipt;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;
use Liberu\Accounting\ReceiptManagement\Models\ReceiptAudit;

final class IngestReceipt
{
    public function handle(array $attributes): Receipt
    {
        if (blank($attributes['file_ref'] ?? null)) {
            throw new InvalidReceipt('file_ref is required.');
        }

        return DB::transaction(function () use ($attributes): Receipt {
            $receipt = Receipt::query()->where('file_ref', $attributes['file_ref'])->where('team_id', $attributes['team_id'] ?? null)->first() ?? Receipt::create(['file_ref' => $attributes['file_ref'], 'team_id' => $attributes['team_id'] ?? null, 'source_type' => $attributes['source_type'] ?? null, 'source_id' => $attributes['source_id'] ?? null, 'merchant' => $attributes['merchant'] ?? null, 'amount' => $attributes['amount'] ?? null, 'currency' => $attributes['currency'] ?? null, 'receipt_date' => $attributes['receipt_date'] ?? null, 'status' => ReceiptStatus::Inbox, 'retention_until' => $attributes['retention_until'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            ReceiptAudit::create(['receipt_id' => $receipt->id, 'action' => 'ingested', 'actor_ref' => $attributes['actor_ref'] ?? null, 'evidence' => ['file_ref' => $receipt->file_ref], 'created_at' => now()]);

            return $receipt;
        });
    }
}
