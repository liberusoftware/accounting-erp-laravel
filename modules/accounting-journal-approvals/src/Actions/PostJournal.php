<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Actions;

use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

final class PostJournal
{
    public function handle(JournalApproval $approval, bool $emergency = false, ?string $reason = null): JournalApproval
    {
        if ($emergency) {
            if (blank($reason)) {
                throw new InvalidApproval('Emergency posting requires a reason.');
            }$approval->update(['status' => ApprovalStatus::EmergencyPosted, 'emergency_reason' => $reason, 'posted_at' => now()]);

            return $approval->refresh();
        }if ($approval->status !== ApprovalStatus::Approved) {
            throw new InvalidApproval('Only approved journals can be posted.');
        }$approval->update(['status' => ApprovalStatus::Posted, 'posted_at' => now()]);

        return $approval->refresh();
    }
}
