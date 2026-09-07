<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingApi;

use Illuminate\Support\ServiceProvider;

final class BranchLocationAccountingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
