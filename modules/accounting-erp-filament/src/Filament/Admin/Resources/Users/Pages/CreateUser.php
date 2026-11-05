<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    #[\Override]
    protected static string $resource = UserResource::class;
}
