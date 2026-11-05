<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Widgets;

use Filament\Widgets\Widget;
use Liberu\Accounting\ErpFilament\Filament\Admin\Pages\PlatformSetupWizard;

class PlatformSetup extends Widget
{
    protected string $view = 'accounting-erp-filament::filament.admin.widgets.platform-setup';

    protected static ?int $sort = -20;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return PlatformSetupWizard::canAccess();
    }
}
