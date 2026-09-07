<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankFeeds\Enums\FeedTransactionStatus;
use Liberu\Accounting\BankFeeds\Events\BankFeedImported;
use Liberu\Accounting\BankFeeds\Exceptions\InvalidBankFeed;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Accounting\BankFeeds\Models\BankFeedTransaction;

final class ImportBankFeedTransactions
{
    /** @param array{added?: list<array<string,mixed>>, modified?: list<array<string,mixed>>, removed?: list<string>, next_cursor?: string|null} $batch */
    public function handle(BankFeedConnection $connection, array $batch): array
    {
        return DB::transaction(function () use ($connection, $batch): array {
            $imported = 0;
            $duplicates = 0;
            foreach (array_merge($batch['added'] ?? [], $batch['modified'] ?? []) as $data) {
                $externalId = trim((string) ($data['external_id'] ?? $data['transaction_id'] ?? ''));
                if ($externalId === '' || blank($data['amount'] ?? null) || blank($data['currency'] ?? null) || blank($data['transaction_date'] ?? null)) {
                    throw new InvalidBankFeed('Every imported transaction requires an external id, amount, currency, and date.');
                }
                $transaction = BankFeedTransaction::query()->where('connection_id', $connection->getKey())->where('external_id', $externalId)->lockForUpdate()->first();
                if ($transaction !== null) {
                    $duplicates++;
                }
                BankFeedTransaction::query()->updateOrCreate(
                    ['connection_id' => $connection->getKey(), 'external_id' => $externalId],
                    ['team_id' => $connection->team_id, 'mapping_id' => $data['mapping_id'] ?? null, 'transaction_date' => $data['transaction_date'], 'posted_date' => $data['posted_date'] ?? null, 'description' => $data['description'] ?? null, 'amount' => $data['amount'], 'currency' => strtoupper((string) $data['currency']), 'status' => ($data['status'] ?? 'posted') === 'pending' ? FeedTransactionStatus::Pending : FeedTransactionStatus::Posted, 'category' => $data['category'] ?? null, 'raw_data' => $data['raw_data'] ?? $data, 'metadata' => $data['metadata'] ?? null]
                );
                $imported++;
            }
            foreach ($batch['removed'] ?? [] as $externalId) {
                BankFeedTransaction::query()->where('connection_id', $connection->getKey())->where('external_id', $externalId)->update(['status' => FeedTransactionStatus::Removed]);
            }
            $connection->update(['cursor' => $batch['next_cursor'] ?? $connection->cursor, 'last_synced_at' => now(), 'last_error' => null, 'last_error_at' => null]);
            DB::afterCommit(fn (): mixed => event(new BankFeedImported($connection->refresh(), $imported, $duplicates)));

            return compact('imported', 'duplicates');
        });
    }
}
