<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource;

final class ChartOfAccountsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-chart-of-accounts';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountResource::class]);
    }

    public function boot(Panel $panel): void {}
}
