<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards;

use Illuminate\Support\ServiceProvider;

final class CorporateCardsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
