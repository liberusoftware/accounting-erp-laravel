<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsFilament\Pages;

use Filament\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\FinancialStatements\Queries\StatementQuery;

final class FinancialStatements extends Page
{
    protected static ?string $navigationLabel = 'Financial statements';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'accounting-financial-statements-filament::financial-statements';

    protected static ?string $slug = 'financial-statements/{bookId}';

    public int $bookId;

    public function mount(int $bookId): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view financial statements.');
        }
        $this->bookId = $bookId;
    }

    protected function getViewData(): array
    {
        return ['statement' => app(StatementQuery::class)->profitAndLoss($this->bookId, now()->startOfYear()->toDateString(), now()->toDateString())];
    }
}
