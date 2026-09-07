<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Events\CreditControlChanged;
use Liberu\Accounting\AccountsReceivable\Exceptions\InvalidReceivable;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableAccount;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class SetCreditControl
{
    public function handle(int $partyId, ?float $creditLimit = null, ?bool $hold = null, ?string $reason = null): ReceivableAccount
    {
        if ($creditLimit !== null && $creditLimit < 0) {
            throw new \InvalidArgumentException('Credit limit cannot be negative.');
        }

        $account = DB::transaction(function () use ($partyId, $creditLimit, $hold, $reason): ReceivableAccount {
            if (! Party::query()->whereKey($partyId)->where('type', PartyType::Customer)->exists()) {
                throw new InvalidReceivable('Credit control can only be configured for an existing customer.');
            }

            $account = ReceivableAccount::query()->where('party_id', $partyId)->lockForUpdate()->first()
                ?? app(EnsureAccount::class)->handle($partyId);
            $account->update(array_filter(['credit_limit' => $creditLimit, 'credit_hold' => $hold, 'hold_reason' => $reason], static fn ($v) => $v !== null));
            DB::afterCommit(fn (): mixed => event(new CreditControlChanged(ReceivableAccount::query()->findOrFail($account->getKey()))));

            return $account->refresh();
        });

        return $account;
    }
}
