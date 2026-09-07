<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

/** @mixin JournalApproval */
final class JournalApprovalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var JournalApproval $approval */
        $approval = $this->resource;

        return [
            'id' => $approval->getKey(), 'approval_ref' => $approval->approval_ref,
            'journal_type' => $approval->journal_type, 'journal_source' => $approval->journal_source,
            'journal_ref' => $approval->journal_ref, 'preparer_ref' => $approval->preparer_ref,
            'reviewer_ref' => $approval->reviewer_ref, 'currency' => $approval->currency,
            'amount' => $approval->amount, 'threshold_amount' => $approval->threshold_amount,
            'status' => $approval->status->value, 'submitted_at' => $approval->submitted_at,
            'decided_at' => $approval->decided_at, 'posted_at' => $approval->posted_at,
            'emergency_reason' => $approval->emergency_reason,
            'decisions' => $this->whenLoaded('decisions'), 'evidence' => $this->whenLoaded('evidence'),
        ];
    }
}
