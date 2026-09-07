<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\MatchingIntelligenceFilament\Resources\MatchingSuggestionResource;

final class ListMatchingSuggestions extends ListRecords
{
    protected static string $resource = MatchingSuggestionResource::class;
}
