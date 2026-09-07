<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ReceiptManagement\Enums\ReceiptStatus;
use Liberu\Accounting\ReceiptManagement\Models\MissingReceiptRequest;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;

final class RequestMissingReceipt
{
    public function handle(array $attributes): MissingReceiptRequest
    {
        return DB::transaction(function () use ($attributes): MissingReceiptRequest {
            $request = MissingReceiptRequest::create(['team_id' => $attributes['team_id'] ?? null, 'receipt_id' => $attributes['receipt_id'] ?? null, 'requestee_ref' => $attributes['requestee_ref'], 'target_type' => $attributes['target_type'], 'target_id' => (string) $attributes['target_id'], 'reason' => $attributes['reason'] ?? null, 'status' => 'open', 'due_on' => $attributes['due_on'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            if (isset($attributes['receipt_id'])) {
                Receipt::whereKey($attributes['receipt_id'])->update(['status' => ReceiptStatus::Requested]);
            }

            return $request;
        });
    }
}
