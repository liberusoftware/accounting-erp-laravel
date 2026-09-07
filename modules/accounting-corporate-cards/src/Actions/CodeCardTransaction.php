<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Actions;

use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;
use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;
use Liberu\Accounting\CorporateCards\Models\CardTransaction;

final class CodeCardTransaction
{
    public function handle(CardTransaction $transaction, string $category, ?string $receipt = null): CardTransaction
    {
        if (blank($category) || $transaction->status === CardTransactionStatus::Reconciled) {
            throw new InvalidCorporateCard('A category is required and reconciled transactions cannot be changed.');
        } $transaction->update(['category_ref' => $category, 'receipt_ref' => $receipt, 'status' => CardTransactionStatus::PendingApproval]);

        return $transaction->fresh();
    }
}
