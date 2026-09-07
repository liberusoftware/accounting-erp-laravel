<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\MatchingIntelligenceFilament\Resources\MatchingSuggestionResource;

final class MatchingIntelligenceFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-matching-intelligence-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([MatchingSuggestionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
