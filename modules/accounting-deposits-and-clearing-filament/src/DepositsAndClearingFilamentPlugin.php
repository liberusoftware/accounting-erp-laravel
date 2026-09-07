<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\DepositsAndClearingFilament\Resources\ClearingDepositResource;

final class DepositsAndClearingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-deposits-and-clearing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ClearingDepositResource::class]);
    }

    public function boot(Panel $panel): void {}
}
