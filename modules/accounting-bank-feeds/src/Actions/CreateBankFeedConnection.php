<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankFeeds\Enums\ConnectionStatus;
use Liberu\Accounting\BankFeeds\Events\BankFeedConnectionCreated;
use Liberu\Accounting\BankFeeds\Exceptions\InvalidBankFeed;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Accounting\BankFeeds\Models\BankFeedInstitution;

final class CreateBankFeedConnection
{
    public function handle(array $attributes): BankFeedConnection
    {
        return DB::transaction(function () use ($attributes): BankFeedConnection {
            $provider = trim((string) ($attributes['provider'] ?? ''));
            $externalReference = trim((string) ($attributes['external_reference'] ?? ''));
            $institution = BankFeedInstitution::query()->find($attributes['institution_id'] ?? null);
            if ($provider === '' || $externalReference === '' || $institution === null || $institution->provider !== $provider || blank($attributes['access_token'] ?? null)) {
                throw new InvalidBankFeed('A connection requires a provider, institution, external reference, and credential.');
            }
            if (BankFeedConnection::query()->where('provider', $provider)->where('external_reference', $externalReference)->exists()) {
                throw new InvalidBankFeed('This provider connection already exists.');
            }
            $connection = BankFeedConnection::query()->create(array_merge($attributes, ['provider' => $provider, 'external_reference' => $externalReference, 'status' => ConnectionStatus::Active]));
            DB::afterCommit(fn (): mixed => event(new BankFeedConnectionCreated(BankFeedConnection::query()->findOrFail($connection->getKey()))));

            return $connection->refresh();
        });
    }
}
