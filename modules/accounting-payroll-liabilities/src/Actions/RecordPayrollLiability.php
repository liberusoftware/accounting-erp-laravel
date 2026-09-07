<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollLiabilities\Enums\LiabilityStatus;
use Liberu\Accounting\PayrollLiabilities\Exceptions\InvalidLiability;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;

final class RecordPayrollLiability
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): PayrollLiability
    {
        $amount = (float) ($attributes['amount'] ?? 0);
        $paid = (float) ($attributes['paid_amount'] ?? 0);
        if (blank($attributes['liability_ref'] ?? null) || $amount <= 0 || $paid < 0 || $paid > $amount) {
            throw new InvalidLiability('Liability reference and valid amount/allocation are required.');
        }

        return DB::transaction(function () use ($attributes, $amount, $paid): PayrollLiability {
            $row = PayrollLiability::query()->firstOrNew(['team_id' => $attributes['team_id'] ?? null, 'liability_ref' => $attributes['liability_ref']]);
            $row->fill(array_merge($attributes, ['amount' => $amount, 'paid_amount' => $paid, 'status' => $attributes['status'] ?? ($paid === $amount ? LiabilityStatus::Paid : LiabilityStatus::Open)]));
            $row->save();

            return $row;
        });
    }
}
