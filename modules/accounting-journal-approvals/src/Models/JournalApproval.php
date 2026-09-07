<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;

/**
 * @property int|string|null $team_id
 * @property string $approval_ref
 * @property string $journal_type
 * @property string $journal_source
 * @property string $journal_ref
 * @property string $preparer_ref
 * @property string|null $reviewer_ref
 * @property string $currency
 * @property string $amount
 * @property string|null $threshold_amount
 * @property ApprovalStatus $status
 * @property Carbon|null $submitted_at
 * @property Carbon|null $decided_at
 * @property Carbon|null $posted_at
 * @property string|null $emergency_reason
 */ final class JournalApproval extends Model
{
    protected $table = 'accounting_journal_approvals';

    protected $fillable = ['team_id', 'approval_ref', 'journal_type', 'journal_source', 'journal_ref', 'preparer_ref', 'reviewer_ref', 'currency', 'amount', 'threshold_amount', 'status', 'submitted_at', 'decided_at', 'posted_at', 'emergency_reason', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'threshold_amount' => 'decimal:2', 'status' => ApprovalStatus::class, 'submitted_at' => 'datetime', 'decided_at' => 'datetime', 'posted_at' => 'datetime', 'metadata' => 'array'];

    public function decisions(): HasMany
    {
        return $this->hasMany(JournalDecision::class, 'approval_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(JournalEvidence::class, 'approval_id');
    }
}
