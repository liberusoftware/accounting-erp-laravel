<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\MultiEntityFilament\Resources\EntityBookResource;

final class MultiEntityFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-multi-entity';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([EntityBookResource::class]);
    }

    public function boot(Panel $panel): void {}
}
