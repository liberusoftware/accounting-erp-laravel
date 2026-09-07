<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant;

use Illuminate\Support\ServiceProvider;

final class CashCollectionAssistantServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
