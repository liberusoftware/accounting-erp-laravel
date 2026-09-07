<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatApi;

use Illuminate\Support\ServiceProvider;

final class VatApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
