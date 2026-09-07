<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PayrollLiabilitiesFilament\Resources\PayrollLiabilityResource;

final class PayrollLiabilitiesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-payroll-liabilities';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PayrollLiabilityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
