<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\MultiCurrencyFilament\Resources\RevaluationResource;

final class MultiCurrencyFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-multi-currency';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([RevaluationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
