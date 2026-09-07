<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsFilament\Resources\DimensionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Dimensions\Actions\SaveDimension;
use Liberu\Accounting\DimensionsFilament\Resources\DimensionResource;

final class CreateDimension extends CreateRecord
{
    protected static string $resource = DimensionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SaveDimension::class)->handle($data);
    }
}
