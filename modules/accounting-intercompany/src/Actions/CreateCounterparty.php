<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyCounterparty;

final class CreateCounterparty
{
    public function handle(array $a): IntercompanyCounterparty
    {
        foreach (['entity_ref', 'counterparty_ref', 'name', 'default_currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidIntercompany("Missing counterparty field [{$k}].");
            }
        }

        return IntercompanyCounterparty::create(['team_id' => $a['team_id'] ?? null, 'entity_ref' => $a['entity_ref'], 'counterparty_ref' => $a['counterparty_ref'], 'name' => $a['name'], 'default_currency' => strtoupper($a['default_currency']), 'active' => $a['active'] ?? true, 'metadata' => $a['metadata'] ?? null]);
    }
}
