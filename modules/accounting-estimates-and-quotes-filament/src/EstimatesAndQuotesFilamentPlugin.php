<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\EstimatesAndQuotesFilament\Resources\EstimateResource;

final class EstimatesAndQuotesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-estimates-and-quotes-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([EstimateResource::class]);
    }

    public function boot(Panel $panel): void {}
}
