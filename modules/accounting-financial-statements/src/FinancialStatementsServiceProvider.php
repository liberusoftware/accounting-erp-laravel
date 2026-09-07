<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatements;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\FinancialStatements\Queries\StatementQuery;

final class FinancialStatementsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StatementQuery::class);
    }
}
