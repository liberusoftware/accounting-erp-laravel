<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionFilament;

use Illuminate\Support\ServiceProvider;

final class RevenueRecognitionFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RevenueRecognitionFilamentPlugin::class);
    }
}
