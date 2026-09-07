<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\LeasesFilament\Resources\LeaseResource;

final class LeasesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-leases-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([LeaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
