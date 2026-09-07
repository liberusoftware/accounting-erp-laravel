<?php

namespace Liberu\Accounting\CoreFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\CoreFilament\Resources\BookResource;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource;

final class AccountingCoreFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([LegalEntityResource::class, BookResource::class]);
    }

    public function boot(Panel $panel): void {}
}
