<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\MatchingIntelligence\Enums\FeedbackType;

final class MatchingFeedback extends Model
{
    protected $table = 'accounting_matching_feedback';

    protected $fillable = ['suggestion_id', 'actor_ref', 'feedback_type', 'comment', 'features', 'metadata'];

    protected $casts = ['feedback_type' => FeedbackType::class, 'features' => 'array', 'metadata' => 'array'];

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(MatchingSuggestion::class, 'suggestion_id');
    }
}
