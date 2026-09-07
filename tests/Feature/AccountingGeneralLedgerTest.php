<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\GeneralLedger\Actions\CreateAccrual;
use Liberu\Accounting\GeneralLedger\Actions\CreateAllocation;
use Liberu\Accounting\GeneralLedger\Actions\CreateCorrection;
use Liberu\Accounting\GeneralLedger\Actions\CreateJournal;
use Liberu\Accounting\GeneralLedger\Actions\CreatePrepayment;
use Liberu\Accounting\GeneralLedger\Actions\GenerateRecurringJournal;
use Liberu\Accounting\GeneralLedger\Actions\PostJournal;
use Liberu\Accounting\GeneralLedger\Actions\ReverseJournal;
use Liberu\Accounting\GeneralLedger\Actions\SaveRecurringJournal;
use Liberu\Accounting\GeneralLedger\Enums\JournalStatus;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Tests\TestCase;

class AccountingGeneralLedgerTest extends TestCase
{
    use RefreshDatabase;

    private function book(): Book
    {
        $entity = LegalEntity::create(['name' => 'Ledger Entity', 'currency_code' => 'USD']);

        return Book::create(['legal_entity_id' => $entity->id, 'name' => 'Main Book', 'code' => 'MAIN', 'accounting_basis' => 'accrual']);
    }

    private function lines(Book $book): array
    {
        $cash = Account::create(['legal_entity_id' => $book->legal_entity_id, 'code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);
        $revenue = Account::create(['legal_entity_id' => $book->legal_entity_id, 'code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'normal_balance' => 'credit']);

        return [['account_id' => $cash->id, 'debit' => 100, 'credit' => 0], ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100]];
    }

    public function test_posted_journals_are_immutable_and_reversible(): void
    {
        $book = $this->book();
        $journal = app(CreateJournal::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $this->lines($book));
        $posted = app(PostJournal::class)->handle($journal, 'actor-1');

        $this->assertSame(JournalStatus::Posted, $posted->status);
        $this->expectException(\LogicException::class);
        $posted->update(['description' => 'forbidden edit']);
    }

    public function test_reversal_creates_a_posted_opposite_entry(): void
    {
        $book = $this->book();
        $journal = app(CreateJournal::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $this->lines($book));
        $posted = app(PostJournal::class)->handle($journal);
        $reversal = app(ReverseJournal::class)->handle($posted);

        $this->assertSame(JournalStatus::Reversed, $posted->fresh()->status);
        $this->assertSame(JournalStatus::Posted, $reversal->status);
        $this->assertSame($posted->id, $reversal->reversal_of_id);
    }

    public function test_recurring_journal_generates_once_and_advances_schedule(): void
    {
        $book = $this->book();
        $template = app(SaveRecurringJournal::class)->handle(['book_id' => $book->id, 'name' => 'Monthly close', 'frequency' => 'monthly', 'next_run_on' => '2026-08-24', 'lines' => $this->lines($book)]);
        $journal = app(GenerateRecurringJournal::class)->handle($template);

        $this->assertSame('recurring', $journal->journal_type->value);
        $this->assertSame('2026-09-24', $template->fresh()->next_run_on->toDateString());
    }

    public function test_specialized_journal_actions_preserve_explicit_types_and_balance_rules(): void
    {
        $book = $this->book();
        $lines = $this->lines($book);
        $entries = [
            app(CreateCorrection::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $lines),
            app(CreateAllocation::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $lines),
            app(CreateAccrual::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $lines),
            app(CreatePrepayment::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-08-24'], $lines),
        ];

        $this->assertSame(['correction', 'allocation', 'accrual', 'prepayment'], array_map(fn (JournalEntry $entry): string => $entry->journal_type->value, $entries));
        $this->assertTrue(collect($entries)->every(fn (JournalEntry $entry): bool => $entry->isBalanced()));
    }
}
