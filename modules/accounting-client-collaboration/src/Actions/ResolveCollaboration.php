<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Actions;

use Liberu\Accounting\ClientCollaboration\Enums\CollaborationStatus;
use Liberu\Accounting\ClientCollaboration\Exceptions\InvalidCollaboration;
use Liberu\Accounting\ClientCollaboration\Models\CollaborationThread;

final class ResolveCollaboration
{
    public function approve(CollaborationThread $thread, string $approver): CollaborationThread
    {
        if ($thread->status !== CollaborationStatus::PendingApproval || blank($approver)) {
            throw new InvalidCollaboration('Only pending approvals can be approved.');
        }

        $thread->update(['status' => CollaborationStatus::Approved, 'approvals' => [...($thread->approvals ?? []), ['approved_by' => $approver]]]);

        return $thread->refresh();
    }

    public function close(CollaborationThread $thread): CollaborationThread
    {
        if ($thread->status === CollaborationStatus::Closed) {
            throw new InvalidCollaboration('Thread is already closed.');
        }

        $thread->update(['status' => CollaborationStatus::Closed]);

        return $thread->refresh();
    }
}
