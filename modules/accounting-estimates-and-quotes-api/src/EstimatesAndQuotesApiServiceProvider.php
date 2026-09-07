<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesApi;

use Illuminate\Support\ServiceProvider;

final class EstimatesAndQuotesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
