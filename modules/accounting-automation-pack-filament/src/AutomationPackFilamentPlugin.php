<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\AutomationPackFilament\Resources\AutomationRecipeResource;

final class AutomationPackFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-automation-pack-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AutomationRecipeResource::class]);
    }

    public function boot(Panel $panel): void {}
}
