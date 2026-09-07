<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccounts\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ChartOfAccounts\Enums\AccountType;
use Liberu\Accounting\ChartOfAccounts\Events\AccountCreated;
use Liberu\Accounting\ChartOfAccounts\Exceptions\InvalidAccount;
use Liberu\Accounting\ChartOfAccounts\Exceptions\InvalidAccountHierarchy;
use Liberu\Accounting\ChartOfAccounts\Models\Account;

final class SaveAccount
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string, mixed> $attributes */
    public function handle(array $attributes, ?Account $account = null): Account
    {
        return DB::transaction(function () use ($attributes, $account): Account {
            $account ??= new Account();
            $legalEntityId = $attributes['legal_entity_id'] ?? $account->legal_entity_id;
            $code = trim((string) ($attributes['code'] ?? $account->code));
            $name = trim((string) ($attributes['name'] ?? $account->name));
            $type = $attributes['type'] ?? $account->type?->value;
            if (empty($legalEntityId) || $code === '' || $name === '' || $type === null) {
                throw new InvalidAccount('An account requires a legal entity, code, name, and type.');
            }
            $accountType = AccountType::tryFrom((string) $type);
            if ($accountType === null) {
                throw new InvalidAccount('The account type is not supported.');
            }
            if (Account::query()->where('legal_entity_id', $legalEntityId)->where('code', $code)->when($account->exists, fn ($query) => $query->where($account->getKeyName(), '!=', $account->getKey()))->exists()) {
                throw new InvalidAccount('The account code is already in use for this legal entity.');
            }
            $attributes['legal_entity_id'] = $legalEntityId;
            $attributes['code'] = $code;
            $attributes['name'] = $name;
            if (empty($attributes['normal_balance'])) {
                $attributes['normal_balance'] = $accountType->defaultNormalBalance()->value;
            } elseif ($attributes['normal_balance'] !== $accountType->defaultNormalBalance()->value) {
                throw new InvalidAccount('The normal balance must match the account type.');
            }
            $this->guardHierarchy($account, $attributes['parent_id'] ?? $account->parent_id, $legalEntityId);
            $wasRecentlyCreated = ! $account->exists;
            $account->fill($attributes);
            $account->save();

            if ($wasRecentlyCreated) {
                DB::afterCommit(fn (): mixed => $this->events->dispatch(new AccountCreated(Account::query()->findOrFail($account->getKey()))));
            }

            return $account->refresh();
        });
    }

    private function guardHierarchy(Account $account, mixed $parentId, mixed $legalEntityId): void
    {
        if ($parentId === null) {
            return;
        }

        if ($account->exists && (int) $parentId === (int) $account->getKey()) {
            throw new InvalidAccountHierarchy('An account cannot be its own parent.');
        }

        $parent = Account::query()->find($parentId);
        if ($parent === null || (int) $parent->legal_entity_id !== (int) $legalEntityId || ! $parent->is_active) {
            throw new InvalidAccountHierarchy('The parent account must belong to the same legal entity.');
        }

        while ($parent !== null) {
            if ($account->exists && (int) $parent->getKey() === (int) $account->getKey()) {
                throw new InvalidAccountHierarchy('An account cannot be moved beneath one of its descendants.');
            }
            $parent = $parent->parent;
        }
    }
}
