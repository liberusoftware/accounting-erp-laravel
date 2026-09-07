<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceApi;

use Illuminate\Support\ServiceProvider;

final class MatchingIntelligenceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
