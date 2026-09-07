<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageApi;

use Illuminate\Support\ServiceProvider;

final class MileageApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
