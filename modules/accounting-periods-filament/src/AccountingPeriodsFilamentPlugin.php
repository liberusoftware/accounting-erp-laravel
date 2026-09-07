<?php

namespace Liberu\Accounting\PeriodsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PeriodsFilament\Resources\AccountingPeriodResource;

final class AccountingPeriodsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-periods';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountingPeriodResource::class]);
    }

    public function boot(Panel $panel): void {}
}
