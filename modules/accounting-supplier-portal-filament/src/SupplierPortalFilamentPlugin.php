<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class SupplierPortalFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-supplier-portal';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([Resources\PortalResourceResource::class]);
    }

    public function boot(Panel $panel): void {}
}
