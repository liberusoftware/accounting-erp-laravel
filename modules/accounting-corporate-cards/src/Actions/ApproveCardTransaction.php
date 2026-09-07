<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;
use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;
use Liberu\Accounting\CorporateCards\Models\CardTransaction;

final class ApproveCardTransaction
{
    public function handle(CardTransaction $transaction, int $actor): CardTransaction
    {
        if ($transaction->status !== CardTransactionStatus::PendingApproval) {
            throw new InvalidCorporateCard('Only coded transactions can be approved.');
        } $transaction->update(['status' => CardTransactionStatus::Approved, 'approved_by' => $actor, 'approved_at' => Carbon::now()]);

        return $transaction->fresh();
    }
}
