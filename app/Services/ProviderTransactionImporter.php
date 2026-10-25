<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class ProviderTransactionImporter
{
    public function handle(array $rows, ?int $teamId, callable $mapper): int
    {
        return DB::transaction(function () use ($rows, $teamId, $mapper): int {
            $count = 0;
            foreach ($rows as $row) {
                $attributes = $mapper($row);
                $externalId = (string) $attributes['external_id'];
                if ($externalId === '') {
                    throw new InvalidArgumentException('A provider transaction must have an external identifier.');
                }
                $transaction = Transaction::query()->where('external_id', $externalId)->first();
                if ($transaction !== null && $transaction->team_id !== $teamId) {
                    throw new LogicException('The provider transaction belongs to another team.');
                }
                $transaction ??= new Transaction();
                $transaction->fill([
                    'external_id' => $externalId,
                    'team_id' => $teamId,
                    'transaction_date' => $attributes['date'],
                    'transaction_description' => $attributes['description'],
                    'description' => $attributes['description'],
                    'amount' => $attributes['amount'],
                    'account_id' => $attributes['account_id'],
                    'type' => $attributes['type'],
                ]);
                $transaction->save();
                $count++;
            }

            return $count;
        });
    }

    public function accountId(string $providerColumn, int|string|null $externalId, ?int $teamId = null): ?int
    {
        if (! in_array($providerColumn, ['qbo_id', 'xero_id', 'sage_id'], true)) {
            throw new InvalidArgumentException('Unsupported accounting provider.');
        }
        if ($externalId === null || $externalId === '') {
            return null;
        }
        $account = Account::query()->where($providerColumn, $externalId)->where('team_id', $teamId)->first();

        return $account === null ? null : (int) $account->getKey();
    }
}
