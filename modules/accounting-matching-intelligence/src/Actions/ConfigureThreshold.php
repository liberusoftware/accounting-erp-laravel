<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MatchingIntelligence\Exceptions\InvalidMatch;
use Liberu\Accounting\MatchingIntelligence\Models\AutomationThreshold;

final class ConfigureThreshold
{
    public function handle(array $attributes): AutomationThreshold
    {
        $confidence = (float) ($attributes['minimum_confidence'] ?? -1);
        if (blank($attributes['match_type'] ?? null) || $confidence < 0 || $confidence > 1) {
            throw new InvalidMatch('A threshold requires a match type and confidence from zero to one.');
        }

        return DB::transaction(fn (): AutomationThreshold => AutomationThreshold::updateOrCreate(['team_id' => $attributes['team_id'] ?? null, 'match_type' => $attributes['match_type']], ['minimum_confidence' => $confidence, 'maximum_amount' => $attributes['maximum_amount'] ?? null, 'require_evidence' => $attributes['require_evidence'] ?? true, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
