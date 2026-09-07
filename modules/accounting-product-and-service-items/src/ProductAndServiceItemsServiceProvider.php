<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems;

use Illuminate\Support\ServiceProvider;

final class ProductAndServiceItemsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
