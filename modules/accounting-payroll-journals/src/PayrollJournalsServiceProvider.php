<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals;

use Illuminate\Support\ServiceProvider;

final class PayrollJournalsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
