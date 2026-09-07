<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BillPaymentsFilament\Resources\BillPaymentResource;

final class BillPaymentsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-bill-payments';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BillPaymentResource::class]);
    }

    public function boot(Panel $panel): void {}
}
