<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds;

use Illuminate\Support\ServiceProvider;

final class BankFeedsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
