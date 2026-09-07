<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsFilament;

use Illuminate\Support\ServiceProvider;

final class RecurringTransactionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RecurringTransactionsFilamentPlugin::class);
    }
}
