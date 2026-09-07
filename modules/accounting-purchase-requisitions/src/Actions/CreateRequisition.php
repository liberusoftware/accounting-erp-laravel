<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Exceptions\InvalidRequisition;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;

final class CreateRequisition
{
    public function handle(array $attributes): PurchaseRequisition
    {
        $lines = $attributes['lines'] ?? [];
        $total = round((float) ($attributes['total_amount'] ?? 0), 2);
        if (blank($attributes['requester_ref'] ?? null) || ! is_array($lines) || count($lines) < 1 || $total <= 0 || blank($attributes['currency'] ?? null)) {
            throw new InvalidRequisition('Requester, currency, positive total, and at least one line are required.');
        }

        return DB::transaction(fn (): PurchaseRequisition => PurchaseRequisition::create(['team_id' => $attributes['team_id'] ?? null, 'requester_ref' => $attributes['requester_ref'], 'title' => $attributes['title'] ?? null, 'currency' => strtoupper($attributes['currency']), 'total_amount' => $total, 'lines' => $lines, 'coding' => $attributes['coding'] ?? [], 'budget' => $attributes['budget'] ?? null, 'attachments' => $attributes['attachments'] ?? [], 'status' => RequisitionStatus::Draft, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
