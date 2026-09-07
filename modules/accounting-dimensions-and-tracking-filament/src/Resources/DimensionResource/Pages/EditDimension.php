<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsFilament\Resources\DimensionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Dimensions\Actions\SaveDimension;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Liberu\Accounting\DimensionsFilament\Resources\DimensionResource;

final class EditDimension extends EditRecord
{
    protected static string $resource = DimensionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Dimension) {
            throw new \InvalidArgumentException('The selected record is not a dimension.');
        }

        return app(SaveDimension::class)->handle($data, $record);
    }
}
