<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\DimensionsFilament\Resources\DimensionResource;

final class DimensionsFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'accounting-dimensions-and-tracking';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([DimensionResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new self();
    }
}
