<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments;

use Illuminate\Support\ServiceProvider;

final class CreditNotesAndAdjustmentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
