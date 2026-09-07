<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsApi;

use Illuminate\Support\ServiceProvider;

final class ProductAndServiceItemsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
