<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\IntercompanyFilament\Resources\IntercompanyTransactionResource;

final class IntercompanyFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-intercompany-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([IntercompanyTransactionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
