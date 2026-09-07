<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\GeneralLedger\Enums\JournalStatus;
use Liberu\Accounting\GeneralLedger\Enums\JournalType;
use Liberu\Accounting\GeneralLedger\Events\JournalCreated;
use Liberu\Accounting\GeneralLedger\Exceptions\InvalidJournal;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;

final class CreateJournal
{
    public function handle(array $attributes, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($attributes, $lines) {
            if (empty($attributes['book_id']) || count($lines) < 2) {
                throw new InvalidJournal('A journal requires a book and at least two lines.');
            }
            $book = Book::query()->find($attributes['book_id']);
            if ($book === null) {
                throw new InvalidJournal('The journal book does not exist.');
            }
            $accountIds = collect($lines)->pluck('account_id')->filter()->unique()->values();
            $accounts = Account::query()->whereIn('id', $accountIds)->get()->keyBy('id');
            if ($accounts->count() !== $accountIds->count() || $accounts->contains(fn (Account $account): bool => (int) $account->legal_entity_id !== (int) $book->getAttribute('legal_entity_id'))) {
                throw new InvalidJournal('Every journal line must reference an account in the book legal entity.');
            }
            $debits = 0.0;
            $credits = 0.0;
            foreach ($lines as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
                if ($debit < 0 || $credit < 0 || ($debit > 0 && $credit > 0) || ($debit == 0 && $credit == 0)) {
                    throw new InvalidJournal('Each line must contain either a positive debit or credit.');
                }$debits += $debit;
                $credits += $credit;
            }
            if (abs($debits - $credits) > 0.005) {
                throw new InvalidJournal('Journal debits and credits must balance.');
            }
            $entry = JournalEntry::create($attributes + ['journal_type' => ($attributes['journal_type'] ?? JournalType::General->value), 'status' => JournalStatus::Draft->value]);
            $entry->lines()->createMany($lines);
            DB::afterCommit(fn () => event(new JournalCreated($entry->fresh('lines'))));

            return $entry->load('lines');
        });
    }
}
