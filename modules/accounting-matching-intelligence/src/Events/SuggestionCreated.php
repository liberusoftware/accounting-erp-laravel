<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class SuggestionCreated
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly MatchingSuggestion $suggestion) {}
}
