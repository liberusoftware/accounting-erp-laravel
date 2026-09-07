<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Exceptions\InvalidBankAccount;
use Liberu\Accounting\BankAccounts\Models\BankAccount;

final class UpdateBankAccount
{
    public function handle(BankAccount $account, array $attributes): BankAccount
    {
        return DB::transaction(function () use ($account, $attributes): BankAccount {
            $account = BankAccount::query()->lockForUpdate()->findOrFail($account->getKey());
            if ($account->status === BankAccountStatus::Closed) {
                throw new InvalidBankAccount('Closed bank accounts cannot be changed.');
            }
            if (isset($attributes['currency'])) {
                $attributes['currency'] = Str::upper((string) $attributes['currency']);
                if (strlen($attributes['currency']) !== 3) {
                    throw new InvalidBankAccount('A bank account currency must be a three-letter code.');
                }
            }
            $account->update($attributes);

            return $account->refresh();
        });
    }
}
