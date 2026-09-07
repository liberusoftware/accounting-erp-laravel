# Accounting financial statements

The `accounting-financial-statements` module provides reproducible, book-scoped reports over posted General Ledger entries: profit and loss, balance sheet, cash flow, changes in equity, comparative periods, dimension filters, and journal-line drill-through.

## Public query boundary

`StatementQuery` is the only reporting boundary. It does not mutate ledger data or serialize application models. Every query verifies that the book exists, validates ISO dates and date ordering, bounds dimension keys to safe scalar filters, and includes the applied dimensions in its result. Only `posted` journal entries are included.

```php
$statement = app(\Liberu\Accounting\FinancialStatements\Queries\StatementQuery::class)
    ->profitAndLoss($bookId, '2026-01-01', '2026-01-31', ['department' => 'sales']);
```

Invalid dates, reversed periods, missing books, and malformed dimensions throw `InvalidStatementRequest`. Balance sheets include a `balance_check` value so operators can detect an unbalanced report without guessing from presentation output. Drill-through returns journal identifiers, dates, descriptions, amounts, and dimensions for the selected account.

## Presentation packages

- `module-accounting-financial-statements-api` publishes authenticated, ability-scoped GET operations under `/api/v1/accounting/financial-statements` and RFC 9457-style invalid-request responses.
- `module-accounting-financial-statements-filament` registers an opt-in Filament 5 page at `financial-statements/{bookId}`.
- `module-accounting-financial-statements-livewire` registers the explicit Livewire 4 alias `module-accounting-financial-statements::statements` with authenticated typed state and selectable statement types.

Presentation adapters call `StatementQuery`; they do not reproduce ledger aggregation or access private storage from another module.

## Verification

```bash
php artisan module:validate
php artisan test tests/Feature/AccountingFinancialStatementsTest.php --compact
php -d memory_limit=512M vendor/bin/phpstan analyse modules/accounting-financial-statements modules/accounting-financial-statements-api modules/accounting-financial-statements-filament modules/accounting-financial-statements-livewire --no-progress
```
