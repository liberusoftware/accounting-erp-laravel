<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;
use Liberu\Accounting\Intercompany\Models\TransferPricingEvidence;

final class AddTransferPricingEvidence
{
    public function handle(IntercompanyTransaction $transaction, array $a): TransferPricingEvidence
    {
        foreach (['evidence_ref', 'kind', 'currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidIntercompany("Missing evidence field [{$k}].");
            }
        }

        return TransferPricingEvidence::create(['transaction_id' => $transaction->getKey(), 'evidence_ref' => $a['evidence_ref'], 'kind' => $a['kind'], 'file_ref' => $a['file_ref'] ?? null, 'description' => $a['description'] ?? null, 'arm_length_value' => $a['arm_length_value'] ?? null, 'currency' => strtoupper($a['currency']), 'captured_at' => now(), 'metadata' => $a['metadata'] ?? null]);
    }
}
