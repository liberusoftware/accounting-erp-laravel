<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ManagementReporting\Enums\ReportStatus;
use Liberu\Accounting\ManagementReporting\Enums\ReviewDecision;
use Liberu\Accounting\ManagementReporting\Events\ReportApproved;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class ReviewReport
{
    public function handle(ReportPack $report, string $actor, string $decision, ?string $comment = null): ReportPack
    {
        try {
            $review = ReviewDecision::from($decision);
        } catch (\ValueError) {
            throw new InvalidReport('Review decision is approved, rejected, or requested.');
        }if ($report->status !== ReportStatus::Draft && $report->status !== ReportStatus::InReview) {
            throw new InvalidReport('Only draft or in-review reports can be reviewed.');
        }

        return DB::transaction(function () use ($report, $actor, $review, $comment): ReportPack {
            $report->reviews()->create(['actor_ref' => $actor, 'decision' => $review, 'comment' => $comment, 'reviewed_at' => now()]);
            $status = $review === ReviewDecision::Approved ? ReportStatus::Approved : ($review === ReviewDecision::Rejected ? ReportStatus::Draft : ReportStatus::InReview);
            $report->update(['status' => $status, 'approved_by' => $review === ReviewDecision::Approved ? $actor : null, 'approved_at' => $review === ReviewDecision::Approved ? now() : null]);
            $result = $report->refresh();
            if ($review === ReviewDecision::Approved) {
                DB::afterCommit(fn () => event(new ReportApproved($result)));
            }

            return $result;
        });
    }
}
