<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ThreeWayMatchingFilament\Resources\MatchRecordResource;

final class ListMatches extends ListRecords
{
    protected static string $resource = MatchRecordResource::class;
}
