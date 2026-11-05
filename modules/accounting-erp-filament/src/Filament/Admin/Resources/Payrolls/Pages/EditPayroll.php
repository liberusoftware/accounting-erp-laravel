<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Payrolls\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Payrolls\PayrollResource;

class EditPayroll extends EditRecord
{
    #[\Override]
    protected static string $resource = PayrollResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
