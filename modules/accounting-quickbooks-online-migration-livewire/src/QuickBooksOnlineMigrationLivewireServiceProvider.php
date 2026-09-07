<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigrationLivewire;

use Illuminate\Support\ServiceProvider;

final class QuickBooksOnlineMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-quickbooks-online-migration');
    }
}
