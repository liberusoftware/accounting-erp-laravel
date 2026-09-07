<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsApi;

use Illuminate\Support\ServiceProvider;

final class ReimbursementsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
