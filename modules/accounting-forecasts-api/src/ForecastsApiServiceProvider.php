<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsApi;

use Illuminate\Support\ServiceProvider;

final class ForecastsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
