<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Liberu\Accounting\AccountsPayable\Models\PayableAccount;

final class EnsureAccount
{
    public function handle(int $partyId): PayableAccount
    {
        return PayableAccount::query()->firstOrCreate(['party_id' => $partyId]);
    }
}
