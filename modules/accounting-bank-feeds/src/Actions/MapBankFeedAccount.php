<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\BankFeeds\Exceptions\InvalidBankFeed;
use Liberu\Accounting\BankFeeds\Models\BankFeedAccountMapping;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

final class MapBankFeedAccount
{
    public function handle(BankFeedConnection $connection, array $attributes): BankFeedAccountMapping
    {
        return DB::transaction(function () use ($connection, $attributes): BankFeedAccountMapping {
            $account = BankAccount::query()->find($attributes['bank_account_id'] ?? null);
            $externalId = trim((string) ($attributes['external_account_id'] ?? ''));
            if ($account === null || $externalId === '') {
                throw new InvalidBankFeed('A mapping requires an existing bank account and external account identifier.');
            }
            $mapping = BankFeedAccountMapping::query()->updateOrCreate(
                ['connection_id' => $connection->getKey(), 'external_account_id' => $externalId],
                array_merge($attributes, ['team_id' => $connection->team_id, 'connection_id' => $connection->getKey(), 'external_account_id' => $externalId])
            );

            return $mapping->refresh();
        });
    }
}
