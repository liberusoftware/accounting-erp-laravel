<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsFilament;

use Illuminate\Support\ServiceProvider;

final class PurchaseRequisitionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PurchaseRequisitionsFilamentPlugin::class);
    }
}
