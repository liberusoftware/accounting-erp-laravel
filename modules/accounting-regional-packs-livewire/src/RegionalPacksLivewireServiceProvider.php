<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacksLivewire;

use Illuminate\Support\ServiceProvider;

final class RegionalPacksLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-regional-packs');
    }
}
