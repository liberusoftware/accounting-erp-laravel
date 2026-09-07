<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingApi;

use Illuminate\Support\ServiceProvider;

final class EInvoicingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
