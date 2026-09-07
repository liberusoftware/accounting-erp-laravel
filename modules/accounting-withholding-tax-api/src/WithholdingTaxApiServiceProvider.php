<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxApi;

use Illuminate\Support\ServiceProvider;

final class WithholdingTaxApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
