<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ProjectProfitabilityFilament\Resources\ProjectProfitabilityResource;

final class ProjectProfitabilityFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-project-profitability-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProjectProfitabilityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
