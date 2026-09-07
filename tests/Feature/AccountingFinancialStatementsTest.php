<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\FinancialStatements\Exceptions\InvalidStatementRequest;
use Liberu\Accounting\FinancialStatements\Queries\StatementQuery;
use Liberu\Accounting\GeneralLedger\Actions\CreateJournal;
use Liberu\Accounting\GeneralLedger\Actions\PostJournal;
use Tests\TestCase;

class AccountingFinancialStatementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statements_calculate_posted_ledger_data_and_drill_through(): void
    {
        $entity = LegalEntity::create(['name' => 'Statements Entity', 'currency_code' => 'USD']);
        $book = Book::create(['legal_entity_id' => $entity->id, 'name' => 'Statements Book', 'code' => 'STAT', 'accounting_basis' => 'accrual']);
        $cash = Account::create(['legal_entity_id' => $entity->id, 'code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);
        $revenue = Account::create(['legal_entity_id' => $entity->id, 'code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'normal_balance' => 'credit']);
        $expense = Account::create(['legal_entity_id' => $entity->id, 'code' => '5000', 'name' => 'Expense', 'type' => 'expense', 'normal_balance' => 'debit']);

        app(PostJournal::class)->handle(app(CreateJournal::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-01-15'], [
            ['account_id' => $cash->id, 'debit' => 250, 'credit' => 0], ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 250],
        ]));
        app(PostJournal::class)->handle(app(CreateJournal::class)->handle(['book_id' => $book->id, 'entry_date' => '2026-01-20'], [
            ['account_id' => $expense->id, 'debit' => 100, 'credit' => 0], ['account_id' => $cash->id, 'debit' => 0, 'credit' => 100],
        ]));

        $query = app(StatementQuery::class);
        $pl = $query->profitAndLoss($book->id, '2026-01-01', '2026-01-31');
        $sheet = $query->balanceSheet($book->id, '2026-01-31');
        $comparative = $query->comparative($book->id, '2026-01-01', '2026-01-31', '2025-01-01', '2025-01-31');
        $drillThrough = $query->drillThrough($book->id, $cash->id, '2026-01-01', '2026-01-31');

        $this->assertSame(150.0, $pl['net_income']);
        $this->assertSame(150.0, $sheet['assets']['total']);
        $this->assertSame(150.0, $sheet['total_liabilities_and_equity']);
        $this->assertSame(0.0, $comparative['comparative']['net_income']);
        $this->assertCount(2, $drillThrough);
    }

    public function test_statement_queries_reject_invalid_periods_and_dimensions(): void
    {
        $entity = LegalEntity::create(['name' => 'Validation Entity', 'currency_code' => 'USD']);
        $book = Book::create(['legal_entity_id' => $entity->id, 'name' => 'Validation Book', 'code' => 'VALID', 'accounting_basis' => 'accrual']);
        $query = app(StatementQuery::class);

        $this->expectException(InvalidStatementRequest::class);
        $query->profitAndLoss($book->id, '2026-02-01', '2026-01-01', ['Department' => ['not-scalar']]);
    }

    public function test_authenticated_api_exposes_book_scoped_statements(): void
    {
        $entity = LegalEntity::create(['name' => 'API Statements Entity', 'currency_code' => 'USD']);
        $book = Book::create(['legal_entity_id' => $entity->id, 'name' => 'API Statements Book', 'code' => 'API', 'accounting_basis' => 'accrual']);
        Sanctum::actingAs(User::factory()->create(), ['accounting.financial-statements.read']);

        $this->getJson('/api/v1/accounting/financial-statements/profit-and-loss?book_id='.$book->id.'&start_date=2026-01-01&end_date=2026-01-31')
            ->assertOk()
            ->assertJsonPath('data.type', 'profit_and_loss');

        $this->getJson('/api/v1/accounting/financial-statements/profit-and-loss?book_id='.$book->id.'&start_date=2026-02-01&end_date=2026-01-01')
            ->assertStatus(422);
    }

    public function test_api_requires_the_financial_statements_read_ability(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this->getJson('/api/v1/accounting/financial-statements/profit-and-loss?book_id=1&start_date=2026-01-01&end_date=2026-01-31')
            ->assertForbidden();
    }
}
