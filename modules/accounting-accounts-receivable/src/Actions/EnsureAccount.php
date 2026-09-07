<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableAccount;

final class EnsureAccount
{
    public function handle(int $partyId): ReceivableAccount
    {
        return ReceivableAccount::firstOrCreate(['party_id' => $partyId]);
    }
}
