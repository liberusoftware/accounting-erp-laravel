<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ProjectCostingFilament\Resources\ProjectCostResource;

final class ProjectCostingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-project-costing-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProjectCostResource::class]);
    }

    public function boot(Panel $panel): void {}
}
