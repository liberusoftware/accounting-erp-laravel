<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MatchingEvidence extends Model
{
    protected $table = 'accounting_matching_evidence';

    protected $fillable = ['suggestion_id', 'kind', 'field', 'source_value', 'target_value', 'weight', 'metadata'];

    protected $casts = ['weight' => 'decimal:6', 'metadata' => 'array'];

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(MatchingSuggestion::class, 'suggestion_id');
    }
}
