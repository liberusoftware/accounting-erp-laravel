<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCardsApi;

use Illuminate\Support\ServiceProvider;

final class CorporateCardsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
