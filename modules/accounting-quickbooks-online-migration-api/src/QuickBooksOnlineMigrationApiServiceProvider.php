<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigrationApi;

use Illuminate\Support\ServiceProvider;

final class QuickBooksOnlineMigrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
