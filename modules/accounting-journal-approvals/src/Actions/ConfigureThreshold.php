<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Liberu\Accounting\JournalApprovals\Models\ApprovalThreshold;

final class ConfigureThreshold
{
    public function handle(array $attributes): ApprovalThreshold
    {
        $amount = (float) ($attributes['minimum_amount'] ?? -1);
        if (blank($attributes['journal_type'] ?? null) || $amount < 0 || blank($attributes['reviewer_role'] ?? null)) {
            throw new InvalidApproval('Threshold requires journal type, non-negative amount, and reviewer role.');
        }

        return DB::transaction(fn (): ApprovalThreshold => ApprovalThreshold::updateOrCreate(['team_id' => $attributes['team_id'] ?? null, 'journal_type' => $attributes['journal_type']], ['minimum_amount' => $amount, 'reviewer_role' => $attributes['reviewer_role'], 'required_approvals' => $attributes['required_approvals'] ?? 1, 'emergency_allowed' => $attributes['emergency_allowed'] ?? false, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
