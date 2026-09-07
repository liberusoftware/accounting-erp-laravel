<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\KpiAndGoalsFilament\Resources\KpiGoalResource;

final class KpiAndGoalsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-kpi-and-goals-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([KpiGoalResource::class]);
    }

    public function boot(Panel $panel): void {}
}
