<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\WithholdingTaxFilament\Resources\WithholdingTaxRuleResource;

final class WithholdingTaxFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-withholding-tax-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WithholdingTaxRuleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
