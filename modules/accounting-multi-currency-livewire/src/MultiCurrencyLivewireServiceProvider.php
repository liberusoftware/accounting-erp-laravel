<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyLivewire;

use Illuminate\Support\ServiceProvider;

final class MultiCurrencyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-multi-currency');
    }
}
