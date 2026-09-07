<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;
use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;
use Liberu\Accounting\CorporateCards\Models\CardAccount;
use Liberu\Accounting\CorporateCards\Models\CardTransaction;

final class RecordCardTransaction
{
    public function handle(CardAccount $account, array $attributes): CardTransaction
    {
        foreach (['transaction_ref', 'transaction_date', 'amount', 'currency'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCorporateCard("{$field} is required.");
            }
        } $amount = (float) $attributes['amount'];
        if ($amount <= 0 || ((float) $account->spent_amount + $amount) > (float) $account->limit_amount) {
            throw new InvalidCorporateCard('Transaction exceeds the card limit.');
        }

        return DB::transaction(function () use ($account, $attributes, $amount): CardTransaction {
            $transaction = $account->transactions()->create([...$attributes, 'team_id' => $account->team_id, 'amount' => $amount, 'status' => CardTransactionStatus::Unassigned]);
            $account->increment('spent_amount', $amount);

            return $transaction;
        });
    }
}
