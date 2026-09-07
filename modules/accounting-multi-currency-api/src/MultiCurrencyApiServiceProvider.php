<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyApi;

use Illuminate\Support\ServiceProvider;

final class MultiCurrencyApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
