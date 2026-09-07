<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource;

final class BankAccountsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-bank-accounts';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BankAccountResource::class]);
    }

    public function boot(Panel $panel): void {}
}
