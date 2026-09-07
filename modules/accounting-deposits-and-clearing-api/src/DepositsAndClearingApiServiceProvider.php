<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingApi;

use Illuminate\Support\ServiceProvider;

final class DepositsAndClearingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
