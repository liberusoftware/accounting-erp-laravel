<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Admin\Pages\PlatformSetupWizard;
use Filament\Widgets\Widget;

class PlatformSetup extends Widget
{
    protected string $view = 'filament.admin.widgets.platform-setup';

    protected static ?int $sort = -20;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return PlatformSetupWizard::canAccess();
    }
}
