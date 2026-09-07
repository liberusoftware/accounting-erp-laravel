<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndApi;

use Illuminate\Support\ServiceProvider;

final class YearEndApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
