<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\VatFilament\Resources\VatRecordResource;
use Liberu\Accounting\VatFilament\Resources\VatReturnResource;

final class VatFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-vat-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([VatRecordResource::class, VatReturnResource::class]);
    }

    public function boot(Panel $panel): void {}
}
