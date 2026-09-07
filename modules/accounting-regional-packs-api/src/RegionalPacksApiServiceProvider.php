<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacksApi;

use Illuminate\Support\ServiceProvider;

final class RegionalPacksApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
