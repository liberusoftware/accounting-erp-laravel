<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BankFeedsFilament\Resources\BankFeedConnectionResource;

final class BankFeedsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-bank-feeds';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BankFeedConnectionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
