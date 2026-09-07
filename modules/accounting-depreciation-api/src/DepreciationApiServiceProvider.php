<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationApi;

use Illuminate\Support\ServiceProvider;

final class DepreciationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
