<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PayrollIntegrationFilament\Resources\PayrollImportResource;

final class PayrollIntegrationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-payroll-integration';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PayrollImportResource::class]);
    }

    public function boot(Panel $panel): void {}
}
