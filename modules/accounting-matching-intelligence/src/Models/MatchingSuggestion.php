<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\MatchingIntelligence\Enums\SuggestionStatus;

/**
 * @property SuggestionStatus $status
 * @property float $confidence
 */
final class MatchingSuggestion extends Model
{
    protected $table = 'accounting_matching_suggestions';

    protected $fillable = ['team_id', 'suggestion_ref', 'source_type', 'source_id', 'target_type', 'target_id', 'match_type', 'confidence', 'score', 'status', 'automation_threshold', 'explanation', 'algorithm_version', 'expires_at', 'metadata'];

    protected $casts = ['confidence' => 'decimal:6', 'score' => 'decimal:6', 'automation_threshold' => 'decimal:6', 'status' => SuggestionStatus::class, 'expires_at' => 'datetime', 'metadata' => 'array'];

    public function evidence(): HasMany
    {
        return $this->hasMany(MatchingEvidence::class, 'suggestion_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(MatchingFeedback::class, 'suggestion_id');
    }
}
