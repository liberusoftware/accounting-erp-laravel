<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxFilament;

use Illuminate\Support\ServiceProvider;

final class WithholdingTaxFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WithholdingTaxFilamentPlugin::class);
    }
}
