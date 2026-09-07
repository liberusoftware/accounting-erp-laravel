<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Actions;

use Liberu\Accounting\CodingSuggestions\Enums\CodingSuggestionStatus;
use Liberu\Accounting\CodingSuggestions\Exceptions\InvalidCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;

final class CreateCodingSuggestion
{
    public function handle(array $attributes): CodingSuggestion
    {
        foreach (['team_id', 'source_ref', 'target_type', 'target_ref', 'confidence', 'explanation'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCodingSuggestion("{$field} is required.");
            }
        }

        $confidence = (float) $attributes['confidence'];
        if ($confidence < 0 || $confidence > 1) {
            throw new InvalidCodingSuggestion('Confidence must be between zero and one.');
        }

        return CodingSuggestion::create([...$attributes, 'status' => CodingSuggestionStatus::Pending]);
    }
}
