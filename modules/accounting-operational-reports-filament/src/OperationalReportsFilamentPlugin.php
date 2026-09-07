<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\OperationalReportsFilament\Resources\ReportRunResource;

final class OperationalReportsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-operational-reports';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReportRunResource::class]);
    }

    public function boot(Panel $panel): void {}
}
