<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistantApi;

use Illuminate\Support\ServiceProvider;

final class CashCollectionAssistantApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
