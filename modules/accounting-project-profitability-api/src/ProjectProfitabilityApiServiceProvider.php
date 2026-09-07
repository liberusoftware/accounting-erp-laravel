<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityApi;

use Illuminate\Support\ServiceProvider;

final class ProjectProfitabilityApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
