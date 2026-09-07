<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Enums\DecisionType;
use Liberu\Accounting\JournalApprovals\Events\JournalApproved;
use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

final class DecideJournal
{
    public function handle(JournalApproval $approval, string $actor, bool $approved, ?string $comment = null): JournalApproval
    {
        if ($approval->status !== ApprovalStatus::Pending) {
            throw new InvalidApproval('Only pending journals can be decided.');
        }
        if (! $approved && blank($comment)) {
            throw new InvalidApproval('Rejection requires a comment.');
        }

        return DB::transaction(function () use ($approval, $actor, $approved, $comment): JournalApproval {
            $approval->decisions()->create(['actor_ref' => $actor, 'decision' => $approved ? DecisionType::Approved : DecisionType::Rejected, 'comment' => $comment, 'decided_at' => now()]);
            $approval->update(['status' => $approved ? ApprovalStatus::Approved : ApprovalStatus::Rejected, 'reviewer_ref' => $actor, 'decided_at' => now()]);
            $result = $approval->refresh();
            if ($approved) {
                DB::afterCommit(fn () => event(new JournalApproved($result)));
            }

            return $result;
        });
    }
}
