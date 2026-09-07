<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\InventoryAccountingFilament\Resources\InventoryItemResource;

final class InventoryAccountingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-inventory-accounting-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([InventoryItemResource::class]);
    }

    public function boot(Panel $panel): void {}
}
