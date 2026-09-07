<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTaxApi;

use Illuminate\Support\ServiceProvider;

final class ConstructionTaxApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
