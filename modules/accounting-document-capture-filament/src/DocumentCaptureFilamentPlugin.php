<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\DocumentCaptureFilament\Resources\CapturedDocumentResource;

final class DocumentCaptureFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-document-capture-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CapturedDocumentResource::class]);
    }

    public function boot(Panel $panel): void {}
}
