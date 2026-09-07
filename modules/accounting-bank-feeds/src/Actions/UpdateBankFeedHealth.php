<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankFeeds\Enums\ConnectionStatus;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

final class UpdateBankFeedHealth
{
    public function handle(BankFeedConnection $connection, ?string $error = null): BankFeedConnection
    {
        return DB::transaction(function () use ($connection, $error): BankFeedConnection {
            $connection->update(['status' => $error === null ? ConnectionStatus::Active : ConnectionStatus::Error, 'last_error' => $error, 'last_error_at' => $error === null ? null : now()]);

            return $connection->refresh();
        });
    }
}
