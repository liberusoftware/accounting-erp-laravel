<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Actions;

use Liberu\Accounting\CodingSuggestions\Enums\CodingSuggestionStatus;
use Liberu\Accounting\CodingSuggestions\Exceptions\InvalidCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;

final class ApplySuggestionFeedback
{
    public function handle(CodingSuggestion $suggestion, array $feedback): CodingSuggestion
    {
        if ($suggestion->status !== CodingSuggestionStatus::Pending || ! in_array($feedback['decision'] ?? null, ['accepted', 'rejected'], true)) {
            throw new InvalidCodingSuggestion('Pending suggestions require an accepted or rejected decision.');
        }

        $suggestion->update(['status' => $feedback['decision'], 'feedback' => $feedback]);

        return $suggestion->refresh();
    }
}
