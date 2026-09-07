<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DocumentCapture\Queries\CaptureQuery;

final class DocumentCaptureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CaptureQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
