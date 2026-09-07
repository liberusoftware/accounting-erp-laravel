<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankFeeds\Exceptions\InvalidBankFeed;
use Liberu\Accounting\BankFeeds\Models\BankFeedInstitution;

final class UpsertBankFeedInstitution
{
    public function handle(array $attributes): BankFeedInstitution
    {
        $provider = trim((string) ($attributes['provider'] ?? ''));
        $externalId = trim((string) ($attributes['external_id'] ?? ''));
        if ($provider === '' || $externalId === '' || blank($attributes['name'] ?? null)) {
            throw new InvalidBankFeed('An institution requires a provider, external id, and name.');
        }

        return DB::transaction(fn (): BankFeedInstitution => BankFeedInstitution::query()->updateOrCreate(['provider' => $provider, 'external_id' => $externalId], $attributes));
    }
}
