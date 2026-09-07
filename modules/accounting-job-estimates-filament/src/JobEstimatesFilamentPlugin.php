<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\JobEstimatesFilament\Resources\JobEstimateResource;

final class JobEstimatesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-job-estimates-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([JobEstimateResource::class]);
    }

    public function boot(Panel $panel): void {}
}
