<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JobEstimates\Enums\DecisionType;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Events\EstimateApproved;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class DecideEstimate
{
    public function handle(JobEstimate $estimate, string $actor, bool $approved, ?string $comment = null): JobEstimate
    {
        if ($estimate->status !== EstimateStatus::Submitted) {
            throw new InvalidEstimate('Only submitted estimates can be decided.');
        }if (! $approved && blank($comment)) {
            throw new InvalidEstimate('Rejected estimates require a comment.');
        }

        return DB::transaction(function () use ($estimate, $actor, $approved, $comment): JobEstimate {
            $estimate->approvals()->create(['actor_ref' => $actor, 'decision' => $approved ? DecisionType::Approved : DecisionType::Rejected, 'comment' => $comment, 'decided_at' => now()]);
            $estimate->update(['status' => $approved ? EstimateStatus::Approved : EstimateStatus::Rejected]);
            $result = $estimate->refresh();
            if ($approved) {
                DB::afterCommit(fn () => event(new EstimateApproved($result)));
            }

            return $result;
        });
    }
}
