<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\TaxReturnsFilament\Resources\TaxReturnResource;

final class TaxReturnsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-tax-returns-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TaxReturnResource::class]);
    }

    public function boot(Panel $panel): void {}
}
