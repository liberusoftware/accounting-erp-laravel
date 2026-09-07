<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\MileageFilament\Resources\MileageTripResource;

final class MileageFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-mileage-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([MileageTripResource::class]);
    }

    public function boot(Panel $panel): void {}
}
