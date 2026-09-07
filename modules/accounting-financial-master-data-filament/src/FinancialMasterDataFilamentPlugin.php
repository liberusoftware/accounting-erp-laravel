<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource;

final class FinancialMasterDataFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-financial-master-data';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PartyResource::class, ItemServiceResource::class]);
    }

    public function boot(Panel $panel): void {}
}
