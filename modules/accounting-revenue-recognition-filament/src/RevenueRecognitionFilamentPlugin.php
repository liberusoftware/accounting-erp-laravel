<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\RevenueRecognitionFilament\Resources\RevenueScheduleResource;

final class RevenueRecognitionFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-revenue-recognition-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([RevenueScheduleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
