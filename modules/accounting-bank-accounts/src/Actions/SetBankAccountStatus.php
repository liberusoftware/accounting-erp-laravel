<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Events\BankAccountStatusChanged;
use Liberu\Accounting\BankAccounts\Exceptions\InvalidBankAccount;
use Liberu\Accounting\BankAccounts\Models\BankAccount;

final class SetBankAccountStatus
{
    public function handle(BankAccount $account, BankAccountStatus $status): BankAccount
    {
        return DB::transaction(function () use ($account, $status): BankAccount {
            $account = BankAccount::query()->lockForUpdate()->findOrFail($account->getKey());
            $previous = $account->status;
            if ($previous === BankAccountStatus::Closed && $status !== BankAccountStatus::Closed) {
                throw new InvalidBankAccount('A closed bank account cannot be reopened.');
            }
            if ($status === BankAccountStatus::Closed && $account->current_balance != 0) {
                throw new InvalidBankAccount('A bank account must have a zero balance before it can be closed.');
            }
            $account->update(['status' => $status, 'closed_at' => $status === BankAccountStatus::Closed ? now() : null]);
            DB::afterCommit(fn (): mixed => event(new BankAccountStatusChanged(BankAccount::query()->findOrFail($account->getKey()), $previous->value)));

            return $account->refresh();
        });
    }
}
