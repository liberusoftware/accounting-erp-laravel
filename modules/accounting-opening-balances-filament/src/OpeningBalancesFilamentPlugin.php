<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\OpeningBalancesFilament\Resources\OpeningBalanceBatchResource;

final class OpeningBalancesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-opening-balances';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([OpeningBalanceBatchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
