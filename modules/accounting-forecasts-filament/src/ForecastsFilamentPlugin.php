<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ForecastsFilament\Resources\ForecastResource;

final class ForecastsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-forecasts-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ForecastResource::class]);
    }

    public function boot(Panel $panel): void {}
}
