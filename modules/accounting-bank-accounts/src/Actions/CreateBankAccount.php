<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Enums\BankAccountType;
use Liberu\Accounting\BankAccounts\Events\BankAccountCreated;
use Liberu\Accounting\BankAccounts\Exceptions\InvalidBankAccount;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\Core\Models\LegalEntity;

final class CreateBankAccount
{
    public function handle(array $attributes): BankAccount
    {
        return DB::transaction(function () use ($attributes): BankAccount {
            $entity = LegalEntity::query()->find($attributes['legal_entity_id'] ?? null);
            $type = $attributes['account_type'] ?? null;
            $currency = Str::upper((string) ($attributes['currency'] ?? ''));
            if ($entity === null || blank($attributes['name'] ?? null) || ! in_array((string) $type, array_column(BankAccountType::cases(), 'value'), true) || strlen($currency) !== 3 || (float) ($attributes['opening_balance'] ?? 0) < 0 || (($attributes['opening_date'] ?? null) === null)) {
                throw new InvalidBankAccount('A bank account requires an entity, name, account type, three-letter currency, opening date, and non-negative opening balance.');
            }
            if (BankAccount::query()->where('legal_entity_id', $entity->getKey())->where('name', $attributes['name'])->exists()) {
                throw new InvalidBankAccount('An account with this name already exists for the legal entity.');
            }
            $account = BankAccount::query()->create(array_merge($attributes, ['currency' => $currency, 'status' => BankAccountStatus::Active, 'current_balance' => $attributes['opening_balance'] ?? 0]));
            DB::afterCommit(fn (): mixed => event(new BankAccountCreated(BankAccount::query()->findOrFail($account->getKey()))));

            return $account->refresh();
        });
    }
}
