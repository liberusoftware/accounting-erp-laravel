<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersFilament;

use Illuminate\Support\ServiceProvider;

final class PurchaseOrdersFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PurchaseOrdersFilamentPlugin::class);
    }
}
