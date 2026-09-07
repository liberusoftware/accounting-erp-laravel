<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreFilament\Resources\LegalEntityResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource;

final class CreateLegalEntity extends CreateRecord
{
    protected static string $resource = LegalEntityResource::class;
}
