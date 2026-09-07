<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersApi;

use Illuminate\Support\ServiceProvider;

final class WorkpapersApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
