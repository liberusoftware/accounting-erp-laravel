<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BankReconciliationFilament\Resources\ReconciliationSessionResource;

final class BankReconciliationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-bank-reconciliation';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReconciliationSessionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
