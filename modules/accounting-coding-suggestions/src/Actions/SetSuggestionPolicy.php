<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Actions;

use Liberu\Accounting\CodingSuggestions\Exceptions\InvalidCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;

final class SetSuggestionPolicy
{
    public function handle(CodingSuggestion $suggestion, array $policy): CodingSuggestion
    {
        $threshold = (float) ($policy['minimum_confidence'] ?? -1);
        if ($threshold < 0 || $threshold > 1) {
            throw new InvalidCodingSuggestion('Minimum confidence must be between zero and one.');
        }

        $suggestion->update(['policy' => $policy]);

        return $suggestion->refresh();
    }
}
