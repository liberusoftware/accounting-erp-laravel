<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsFilament;

use Illuminate\Support\ServiceProvider;

final class ProductAndServiceItemsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductAndServiceItemsFilamentPlugin::class);
    }
}
