<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionApi;

use Illuminate\Support\ServiceProvider;

final class RevenueRecognitionApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
