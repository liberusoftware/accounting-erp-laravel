<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\TimeTrackingFilament\Resources\TimeEntryResource;

final class TimeTrackingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-time-tracking-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TimeEntryResource::class]);
    }

    public function boot(Panel $panel): void {}
}
