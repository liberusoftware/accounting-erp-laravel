<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\WorkpapersFilament\Resources\WorkpaperResource;

final class WorkpapersFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-workpapers-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WorkpaperResource::class]);
    }

    public function boot(Panel $panel): void {}
}
