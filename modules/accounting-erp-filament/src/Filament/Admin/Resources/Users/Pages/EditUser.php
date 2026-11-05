<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\UserResource;

class EditUser extends EditRecord
{
    #[\Override]
    protected static string $resource = UserResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
