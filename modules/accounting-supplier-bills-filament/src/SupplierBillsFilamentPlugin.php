<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\SupplierBillsFilament\Resources\SupplierBillResource;

final class SupplierBillsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-supplier-bills';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SupplierBillResource::class]);
    }

    public function boot(Panel $panel): void {}
}
