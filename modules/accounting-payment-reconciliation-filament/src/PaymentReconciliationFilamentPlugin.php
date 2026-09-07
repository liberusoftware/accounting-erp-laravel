<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PaymentReconciliationFilament\Resources\SettlementRunResource;

final class PaymentReconciliationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-payment-reconciliation';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SettlementRunResource::class]);
    }

    public function boot(Panel $panel): void {}
}
