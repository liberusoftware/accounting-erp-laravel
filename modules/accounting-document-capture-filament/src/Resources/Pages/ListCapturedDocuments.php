<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\DocumentCaptureFilament\Resources\CapturedDocumentResource;

final class ListCapturedDocuments extends ListRecords
{
    protected static string $resource = CapturedDocumentResource::class;
}
