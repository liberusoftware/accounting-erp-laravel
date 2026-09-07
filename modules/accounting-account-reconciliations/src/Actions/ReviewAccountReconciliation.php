<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Actions;

use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;
use Liberu\Accounting\AccountReconciliations\Exceptions\InvalidAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class ReviewAccountReconciliation
{
    public function handle(AccountReconciliation $reconciliation, array $reviewer): AccountReconciliation
    {
        if ($reconciliation->status !== ReconciliationStatus::Prepared || blank($reviewer['user_id'] ?? null)) {
            throw new InvalidAccountReconciliation('Only prepared reconciliations can enter review.');
        }

        $reconciliation->update(['reviewer' => $reviewer, 'status' => ReconciliationStatus::InReview]);

        return $reconciliation;
    }
}
