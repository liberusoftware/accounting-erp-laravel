<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyCounterparty;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class CreateTransaction
{
    public function handle(IntercompanyCounterparty $counterparty, array $a): IntercompanyTransaction
    {
        $amount = (float) ($a['amount'] ?? 0);
        foreach (['transaction_ref', 'source_entity_ref', 'target_entity_ref', 'transaction_type', 'description', 'currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidIntercompany("Missing transaction field [{$k}].");
            }
        }if ($amount <= 0 || $a['source_entity_ref'] === $a['target_entity_ref']) {
            throw new InvalidIntercompany('Transaction amount must be positive and entities must differ.');
        }

        return IntercompanyTransaction::create(['team_id' => $a['team_id'] ?? null, 'transaction_ref' => $a['transaction_ref'], 'counterparty_id' => $counterparty->getKey(), 'source_entity_ref' => $a['source_entity_ref'], 'target_entity_ref' => $a['target_entity_ref'], 'transaction_type' => $a['transaction_type'], 'description' => $a['description'], 'amount' => $amount, 'currency' => strtoupper($a['currency']), 'status' => TransactionStatus::PendingConfirmation, 'transaction_date' => $a['transaction_date'] ?? now(), 'metadata' => $a['metadata'] ?? null]);
    }
}
