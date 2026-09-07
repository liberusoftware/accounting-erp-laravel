<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersFilament;

use Illuminate\Support\ServiceProvider;

final class TransfersFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TransfersFilamentPlugin::class, fn (): TransfersFilamentPlugin => TransfersFilamentPlugin::make());
    }
}
