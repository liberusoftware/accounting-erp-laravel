<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankFeeds\Enums\FeedTransactionStatus;
use Liberu\Accounting\BankFeeds\Exceptions\InvalidBankFeed;
use Liberu\Accounting\BankFeeds\Models\BankFeedTransaction;

final class SetBankFeedTransactionStatus
{
    public function handle(BankFeedTransaction $transaction, FeedTransactionStatus $status): BankFeedTransaction
    {
        if ($transaction->status === FeedTransactionStatus::Removed && $status !== FeedTransactionStatus::Removed) {
            throw new InvalidBankFeed('Removed feed transactions cannot be restored through this action.');
        }

        return DB::transaction(function () use ($transaction, $status): BankFeedTransaction {
            $transaction->update(['status' => $status]);

            return $transaction->refresh();
        });
    }
}
