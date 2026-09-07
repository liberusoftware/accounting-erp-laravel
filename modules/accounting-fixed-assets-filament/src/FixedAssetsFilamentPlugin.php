<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\FixedAssetsFilament\Resources\AssetResource;

final class FixedAssetsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-fixed-assets-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AssetResource::class]);
    }

    public function boot(Panel $panel): void {}
}
