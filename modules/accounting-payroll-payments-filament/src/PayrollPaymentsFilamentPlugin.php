<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PayrollPaymentsFilament\Resources\PayrollPaymentBatchResource;

final class PayrollPaymentsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-payroll-payments';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PayrollPaymentBatchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
