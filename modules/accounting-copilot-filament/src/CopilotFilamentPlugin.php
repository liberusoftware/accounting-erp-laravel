<?php

declare(strict_types=1);

namespace Liberu\Accounting\CopilotFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\CopilotFilament\Resources\CopilotRequestResource;

final class CopilotFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-copilot-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CopilotRequestResource::class]);
    }

    public function boot(Panel $panel): void {}
}
