<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesApi;

use Illuminate\Support\ServiceProvider;

final class LeasesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
