<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Accounting\BankFeedsApi\Policies\BankFeedsPolicy;

final class BankFeedsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(BankFeedConnection::class, BankFeedsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
