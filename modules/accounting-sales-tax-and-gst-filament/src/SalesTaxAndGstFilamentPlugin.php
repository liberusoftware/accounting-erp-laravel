<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class SalesTaxAndGstFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-sales-tax-and-gst';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([Resources\SalesTaxResource::class]);
    }

    public function boot(Panel $panel): void {}
}
