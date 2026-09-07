<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;
use Liberu\Accounting\AccountReconciliations\Exceptions\InvalidAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class CertifyAccountReconciliation
{
    public function handle(AccountReconciliation $reconciliation, array $certification): AccountReconciliation
    {
        if ($reconciliation->status !== ReconciliationStatus::InReview || blank($certification['user_id'] ?? null) || blank($certification['attestation'] ?? null)) {
            throw new InvalidAccountReconciliation('Only reviewed reconciliations with an attestation can be certified.');
        }

        return DB::transaction(function () use ($reconciliation, $certification): AccountReconciliation {
            $reconciliation->update(['certification' => [...$certification, 'certified_at' => now()->toISOString()], 'status' => ReconciliationStatus::Certified]);

            return $reconciliation;
        });
    }
}
