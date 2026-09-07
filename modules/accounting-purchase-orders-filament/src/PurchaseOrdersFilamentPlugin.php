<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PurchaseOrdersFilament\Resources\PurchaseOrderResource;

final class PurchaseOrdersFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-purchase-orders';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PurchaseOrderResource::class]);
    }

    public function boot(Panel $panel): void {}
}
