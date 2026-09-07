<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class TaxCoreFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'accounting-tax-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([Resources\TaxRuleResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new self();
    }
}
