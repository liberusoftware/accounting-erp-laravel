<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ProjectBillingFilament\Resources\ProjectBillingResource;

final class ProjectBillingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-project-billing-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProjectBillingResource::class]);
    }

    public function boot(Panel $panel): void {}
}
