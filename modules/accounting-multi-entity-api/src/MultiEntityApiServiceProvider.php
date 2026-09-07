<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityApi;

use Illuminate\Support\ServiceProvider;

final class MultiEntityApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
