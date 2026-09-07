<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Reimbursements\Enums\ReimbursementStatus;
use Liberu\Accounting\Reimbursements\Exceptions\InvalidReimbursement;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;

final class CreateReimbursementLiability
{
    public function handle(array $attributes): ReimbursementLiability
    {
        $amount = round((float) ($attributes['amount'] ?? 0), 2);
        if (blank($attributes['payee_ref'] ?? null) || $amount <= 0 || blank($attributes['currency'] ?? null)) {
            throw new InvalidReimbursement('An approved liability requires payee, positive amount, and currency.');
        }

        return DB::transaction(fn (): ReimbursementLiability => ReimbursementLiability::create(['team_id' => $attributes['team_id'] ?? null, 'payee_ref' => $attributes['payee_ref'], 'source_type' => $attributes['source_type'] ?? null, 'source_id' => $attributes['source_id'] ?? null, 'kind' => $attributes['kind'] ?? 'expense', 'currency' => strtoupper($attributes['currency']), 'amount' => $amount, 'approved_at' => now(), 'status' => ReimbursementStatus::Approved, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
