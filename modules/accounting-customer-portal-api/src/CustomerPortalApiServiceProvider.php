<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortalApi;

use Illuminate\Support\ServiceProvider;

final class CustomerPortalApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
