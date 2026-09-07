<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ClientCollaboration\Enums\CollaborationStatus;

final class CollaborationThread extends Model
{
    protected $table = 'accounting_collaboration_threads';

    protected $fillable = ['team_id', 'thread_ref', 'kind', 'subject', 'status', 'participants', 'messages', 'approvals', 'reminders', 'evidence'];

    protected $casts = ['status' => CollaborationStatus::class, 'participants' => 'array', 'messages' => 'array', 'approvals' => 'array', 'reminders' => 'array', 'evidence' => 'array'];
}
