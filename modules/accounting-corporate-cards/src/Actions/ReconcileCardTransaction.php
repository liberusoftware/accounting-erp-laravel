<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Actions;

use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;
use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;
use Liberu\Accounting\CorporateCards\Models\CardTransaction;

final class ReconcileCardTransaction
{
    public function handle(CardTransaction $transaction, string $reference): CardTransaction
    {
        if ($transaction->status !== CardTransactionStatus::Approved || blank($reference)) {
            throw new InvalidCorporateCard('Only approved transactions with a reconciliation reference can be reconciled.');
        } $transaction->update(['status' => CardTransactionStatus::Reconciled, 'reconciliation_ref' => $reference]);

        return $transaction->fresh('cardAccount');
    }
}
