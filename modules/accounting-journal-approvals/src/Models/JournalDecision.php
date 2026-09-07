<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\JournalApprovals\Enums\DecisionType;

final class JournalDecision extends Model
{
    protected $table = 'accounting_journal_decisions';

    protected $fillable = ['approval_id', 'actor_ref', 'decision', 'comment', 'decided_at', 'metadata'];

    protected $casts = ['decision' => DecisionType::class, 'decided_at' => 'datetime', 'metadata' => 'array'];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(JournalApproval::class, 'approval_id');
    }
}
