<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyDifference;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class RecordDifference
{
    public function handle(IntercompanyTransaction $transaction, array $a): IntercompanyDifference
    {
        $amount = (float) ($a['amount'] ?? 0);
        if ($amount <= 0 || blank($a['difference_ref'] ?? null) || blank($a['reason'] ?? null)) {
            throw new InvalidIntercompany('Difference requires a positive amount, reference and reason.');
        }$transaction->update(['status' => 'disputed']);

        return IntercompanyDifference::create(['transaction_id' => $transaction->getKey(), 'difference_ref' => $a['difference_ref'], 'amount' => $amount, 'reason' => $a['reason'], 'status' => 'open', 'actor_ref' => $a['actor_ref'] ?? 'system']);
    }
}
