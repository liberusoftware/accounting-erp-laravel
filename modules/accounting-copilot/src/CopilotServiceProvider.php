<?php

declare(strict_types=1);

namespace Liberu\Accounting\Copilot;

use Illuminate\Support\ServiceProvider;

final class CopilotServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
