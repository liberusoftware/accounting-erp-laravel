<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration;

use Illuminate\Support\ServiceProvider;

final class QuickBooksOnlineMigrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
