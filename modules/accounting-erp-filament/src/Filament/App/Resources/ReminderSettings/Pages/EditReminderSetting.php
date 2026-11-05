<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ReminderSettings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ReminderSettings\ReminderSettingResource;

class EditReminderSetting extends EditRecord
{
    #[\Override]
    protected static string $resource = ReminderSettingResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
