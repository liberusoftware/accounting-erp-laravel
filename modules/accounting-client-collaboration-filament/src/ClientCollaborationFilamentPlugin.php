<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaborationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ClientCollaborationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-client-collaboration-filament';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
