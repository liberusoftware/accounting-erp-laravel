<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsApi;

use Illuminate\Support\ServiceProvider;

final class TaxReturnsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
