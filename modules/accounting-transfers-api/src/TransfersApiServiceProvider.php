<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersApi;

use Illuminate\Support\ServiceProvider;

final class TransfersApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
