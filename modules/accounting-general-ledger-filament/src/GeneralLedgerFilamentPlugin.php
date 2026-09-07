<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource;

final class GeneralLedgerFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'accounting-general-ledger';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([JournalResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new self();
    }
}
