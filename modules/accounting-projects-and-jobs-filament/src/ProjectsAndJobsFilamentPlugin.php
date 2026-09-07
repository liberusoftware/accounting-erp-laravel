<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ProjectsAndJobsFilament\Resources\ProjectJobResource;

final class ProjectsAndJobsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-projects-and-jobs-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProjectJobResource::class]);
    }

    public function boot(Panel $panel): void {}
}
