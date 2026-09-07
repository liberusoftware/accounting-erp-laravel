<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\AccountsPayable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayableFilament\Resources\PayableOpenItemResource;

final class CreatePayable extends CreateRecord
{
    protected static string $resource = PayableOpenItemResource::class;

    protected function handleRecordCreation(array $data): PayableOpenItem
    {
        return app(CreateOpenItem::class)->handle($data);
    }
}
