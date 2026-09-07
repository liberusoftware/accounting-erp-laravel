<?php

declare(strict_types=1);

namespace Liberu\Accounting\Revolut;

use Illuminate\Support\ServiceProvider;
use Liberu\Foundation\Integrations\Support\IntegrationRegistry;

final class RevolutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RevolutAdapter::class);
    }

    public function boot(): void
    {
        if ($this->app->bound(IntegrationRegistry::class)) {
            $this->app->make(IntegrationRegistry::class)->register($this->app->make(RevolutAdapter::class));
        }
    }
}
