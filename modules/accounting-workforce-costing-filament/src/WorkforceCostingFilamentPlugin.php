<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\WorkforceCostingFilament\Resources\WorkforceCostingRuleResource;
use Liberu\Accounting\WorkforceCostingFilament\Resources\WorkforceCostResource;

final class WorkforceCostingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-workforce-costing-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WorkforceCostResource::class, WorkforceCostingRuleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
