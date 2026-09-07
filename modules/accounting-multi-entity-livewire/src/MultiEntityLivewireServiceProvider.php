<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityLivewire;

use Illuminate\Support\ServiceProvider;

final class MultiEntityLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-multi-entity');
    }
}
