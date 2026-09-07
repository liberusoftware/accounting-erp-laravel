<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\TransfersFilament\Resources\TransferResource;

final class TransfersFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-transfers-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TransferResource::class]);
    }

    public function boot(Panel $panel): void {}
}
