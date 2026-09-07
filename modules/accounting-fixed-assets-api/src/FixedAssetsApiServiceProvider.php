<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsApi;

use Illuminate\Support\ServiceProvider;

final class FixedAssetsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
