<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReportingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ContractorReportingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-contractor-reporting-filament';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
