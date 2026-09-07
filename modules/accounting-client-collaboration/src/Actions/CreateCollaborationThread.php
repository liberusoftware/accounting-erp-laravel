<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Actions;

use Liberu\Accounting\ClientCollaboration\Enums\CollaborationStatus;
use Liberu\Accounting\ClientCollaboration\Exceptions\InvalidCollaboration;
use Liberu\Accounting\ClientCollaboration\Models\CollaborationThread;

final class CreateCollaborationThread
{
    public function handle(array $attributes): CollaborationThread
    {
        foreach (['team_id', 'thread_ref', 'kind', 'subject'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCollaboration("{$field} is required.");
            }
        }

        if (! in_array($attributes['kind'], ['document-request', 'question', 'task', 'discussion'], true)) {
            throw new InvalidCollaboration('Unsupported collaboration kind.');
        }

        return CollaborationThread::create([...$attributes, 'status' => CollaborationStatus::Open]);
    }
}
