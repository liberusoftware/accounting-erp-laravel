<?php

declare(strict_types=1);

namespace Liberu\Accounting\Plaid;

use Illuminate\Support\ServiceProvider;
use Liberu\Foundation\Integrations\Support\IntegrationRegistry;

final class PlaidServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlaidBankFeedAdapter::class);
    }

    public function boot(): void
    {
        if ($this->app->bound(IntegrationRegistry::class)) {
            $this->app->make(IntegrationRegistry::class)->register($this->app->make(PlaidBankFeedAdapter::class));
        }
    }
}
