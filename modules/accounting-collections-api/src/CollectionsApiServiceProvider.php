<?php

declare(strict_types=1);

namespace Liberu\Accounting\CollectionsApi;

use Illuminate\Support\ServiceProvider;

final class CollectionsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
