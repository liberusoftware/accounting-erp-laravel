<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorComplianceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ContractorComplianceFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-contractor-compliance-filament';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
