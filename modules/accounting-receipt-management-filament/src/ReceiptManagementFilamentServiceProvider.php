<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementFilament;

use Illuminate\Support\ServiceProvider;

final class ReceiptManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReceiptManagementFilamentPlugin::class);
    }
}
