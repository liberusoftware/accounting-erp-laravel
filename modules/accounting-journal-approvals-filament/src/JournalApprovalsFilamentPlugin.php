<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\JournalApprovalsFilament\Resources\JournalApprovalResource;

final class JournalApprovalsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-journal-approvals-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([JournalApprovalResource::class]);
    }

    public function boot(Panel $panel): void {}
}
