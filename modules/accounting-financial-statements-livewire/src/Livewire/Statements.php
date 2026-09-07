<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsLivewire\Livewire;

use DateTimeImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\FinancialStatements\Enums\StatementType;
use Liberu\Accounting\FinancialStatements\Queries\StatementQuery;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class Statements extends Component
{
    #[Locked]
    public int $bookId;

    #[Locked]
    public string $startDate;

    #[Locked]
    public string $endDate;

    public string $statementType = 'profit_and_loss';

    public function mount(int $bookId, string $startDate, string $endDate): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view financial statements.');
        }
        $this->validateDate($startDate);
        $this->validateDate($endDate);
        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('The statement start date must not be after its end date.');
        }

        $this->bookId = $bookId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function updatedStatementType(string $value): void
    {
        if (StatementType::tryFrom($value) === null) {
            $this->statementType = StatementType::ProfitAndLoss->value;
        }
    }

    public function render(): mixed
    {
        $query = app(StatementQuery::class);
        $statementType = StatementType::from($this->statementType);
        $statement = match ($statementType) {
            StatementType::ProfitAndLoss => $query->profitAndLoss($this->bookId, $this->startDate, $this->endDate),
            StatementType::BalanceSheet => $query->balanceSheet($this->bookId, $this->endDate),
            StatementType::CashFlow => $query->cashFlow($this->bookId, $this->startDate, $this->endDate),
            StatementType::ChangesInEquity => $query->changesInEquity($this->bookId, $this->startDate, $this->endDate),
        };

        return view('accounting-financial-statements-livewire::statements', [
            'statement' => $statement,
            'statementTypes' => StatementType::cases(),
        ]);
    }

    private function validateDate(string $date): void
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new \InvalidArgumentException('Statement dates must use the YYYY-MM-DD format.');
        }
    }
}
