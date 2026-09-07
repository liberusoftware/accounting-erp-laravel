<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\CashCodingFilament\Resources\CashCodingBatchResource;

final class CashCodingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-cash-coding';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CashCodingBatchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
