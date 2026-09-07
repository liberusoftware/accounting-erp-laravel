<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConsolidationApi;

use Illuminate\Support\ServiceProvider;

final class ConsolidationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
