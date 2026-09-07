<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Actions;

use Liberu\Accounting\ClientCollaboration\Enums\CollaborationStatus;
use Liberu\Accounting\ClientCollaboration\Exceptions\InvalidCollaboration;
use Liberu\Accounting\ClientCollaboration\Models\CollaborationThread;

final class RecordCollaborationActivity
{
    public function message(CollaborationThread $thread, array $message): CollaborationThread
    {
        if ($thread->status === CollaborationStatus::Closed || blank($message['body'] ?? null)) {
            throw new InvalidCollaboration('Open threads require a message body.');
        }

        $thread->update(['messages' => [...($thread->messages ?? []), $message]]);

        return $thread->refresh();
    }

    public function approval(CollaborationThread $thread, array $approval): CollaborationThread
    {
        if (blank($approval['approver_ref'] ?? null)) {
            throw new InvalidCollaboration('Approver reference is required.');
        }

        $thread->update(['approvals' => [...($thread->approvals ?? []), $approval], 'status' => CollaborationStatus::PendingApproval]);

        return $thread->refresh();
    }

    public function reminder(CollaborationThread $thread, array $reminder): CollaborationThread
    {
        if (blank($reminder['due_at'] ?? null)) {
            throw new InvalidCollaboration('Reminder due time is required.');
        }

        $thread->update(['reminders' => [...($thread->reminders ?? []), $reminder]]);

        return $thread->refresh();
    }

    public function evidence(CollaborationThread $thread, array $evidence): CollaborationThread
    {
        if (blank($evidence['reference'] ?? null)) {
            throw new InvalidCollaboration('Evidence reference is required.');
        }

        $thread->update(['evidence' => [...($thread->evidence ?? []), $evidence]]);

        return $thread->refresh();
    }
}
