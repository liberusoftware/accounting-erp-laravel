<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\CodingSuggestions\Enums\CodingSuggestionStatus;

final class CodingSuggestion extends Model
{
    protected $table = 'accounting_coding_suggestions';

    protected $fillable = ['team_id', 'source_ref', 'target_type', 'target_ref', 'confidence', 'explanation', 'status', 'feedback', 'policy', 'review'];

    protected $casts = ['confidence' => 'decimal:6', 'status' => CodingSuggestionStatus::class, 'feedback' => 'array', 'policy' => 'array', 'review' => 'array'];
}
