<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesLivewire;

use Illuminate\Support\ServiceProvider;

final class OpeningBalancesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-opening-balances');
    }
}
