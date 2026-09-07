<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ManagementReportingFilament\Resources\ReportPackResource;

final class ManagementReportingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-management-reporting-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReportPackResource::class]);
    }

    public function boot(Panel $panel): void {}
}
