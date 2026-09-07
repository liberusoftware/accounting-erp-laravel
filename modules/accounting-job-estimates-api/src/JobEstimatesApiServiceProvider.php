<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesApi;

use Illuminate\Support\ServiceProvider;

final class JobEstimatesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
