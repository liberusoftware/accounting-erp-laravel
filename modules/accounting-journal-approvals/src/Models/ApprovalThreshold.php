<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Models;

use Illuminate\Database\Eloquent\Model;

final class ApprovalThreshold extends Model
{
    protected $table = 'accounting_journal_approval_thresholds';

    protected $fillable = ['team_id', 'journal_type', 'minimum_amount', 'reviewer_role', 'required_approvals', 'emergency_allowed', 'active', 'metadata'];

    protected $casts = ['minimum_amount' => 'decimal:2', 'required_approvals' => 'integer', 'emergency_allowed' => 'boolean', 'active' => 'boolean', 'metadata' => 'array'];
}
