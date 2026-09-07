<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\YearEndFilament\Resources\YearEndCloseResource;

final class ListYearEndCloses extends ListRecords
{
    protected static string $resource = YearEndCloseResource::class;
}
