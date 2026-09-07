<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\AccountReconciliationsFilament\Resources\AccountReconciliationResource;

final class AccountReconciliationsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-account-reconciliations';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountReconciliationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
