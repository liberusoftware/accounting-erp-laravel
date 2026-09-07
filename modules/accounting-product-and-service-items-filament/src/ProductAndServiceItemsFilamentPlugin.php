<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ProductAndServiceItemsFilament\Resources\AccountingItemResource;

final class ProductAndServiceItemsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-product-and-service-items';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountingItemResource::class]);
    }

    public function boot(Panel $panel): void {}
}
