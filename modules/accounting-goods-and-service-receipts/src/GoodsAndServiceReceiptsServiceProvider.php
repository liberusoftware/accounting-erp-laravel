<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\GoodsAndServiceReceipts\Queries\ReceiptQuery;

final class GoodsAndServiceReceiptsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReceiptQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
