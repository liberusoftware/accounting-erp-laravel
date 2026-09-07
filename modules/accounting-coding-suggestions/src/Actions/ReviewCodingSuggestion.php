<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Actions;

use Liberu\Accounting\CodingSuggestions\Enums\CodingSuggestionStatus;
use Liberu\Accounting\CodingSuggestions\Exceptions\InvalidCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;

final class ReviewCodingSuggestion
{
    public function handle(CodingSuggestion $suggestion, array $review): CodingSuggestion
    {
        if (! in_array($suggestion->status, [CodingSuggestionStatus::Accepted, CodingSuggestionStatus::Rejected], true) || blank($review['reviewer_ref'] ?? null)) {
            throw new InvalidCodingSuggestion('A reviewed suggestion must have a feedback decision and reviewer.');
        }

        $suggestion->update(['status' => CodingSuggestionStatus::Reviewed, 'review' => $review]);

        return $suggestion->refresh();
    }
}
