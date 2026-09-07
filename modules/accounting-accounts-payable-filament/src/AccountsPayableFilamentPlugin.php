<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\AccountsPayableFilament\Resources\PayableOpenItemResource;

final class AccountsPayableFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-accounts-payable';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PayableOpenItemResource::class]);
    }

    public function boot(Panel $panel): void {}
}
