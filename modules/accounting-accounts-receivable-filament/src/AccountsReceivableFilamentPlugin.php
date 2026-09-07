<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\AccountsReceivableFilament\Resources\ReceivableOpenItemResource;

final class AccountsReceivableFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-accounts-receivable';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReceivableOpenItemResource::class]);
    }

    public function boot(Panel $panel): void {}
}
