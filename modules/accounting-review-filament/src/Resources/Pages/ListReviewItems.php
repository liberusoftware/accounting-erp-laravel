<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ReviewFilament\Resources\ReviewItemResource;

final class ListReviewItems extends ListRecords
{
    protected static string $resource = ReviewItemResource::class;
}
