<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsApi;

use Illuminate\Support\ServiceProvider;

final class GoodsAndServiceReceiptsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
