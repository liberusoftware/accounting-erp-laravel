<?php

namespace Liberu\Accounting\ErpFilament;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class ErpFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::component('app-layout', View\Components\AppLayout::class);
        Blade::component('guest-layout', View\Components\GuestLayout::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-erp-filament');
    }
}
