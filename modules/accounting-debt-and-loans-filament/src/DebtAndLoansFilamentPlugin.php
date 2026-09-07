<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\DebtAndLoansFilament\Resources\DebtFacilityResource;

final class DebtAndLoansFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-debt-and-loans';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([DebtFacilityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
