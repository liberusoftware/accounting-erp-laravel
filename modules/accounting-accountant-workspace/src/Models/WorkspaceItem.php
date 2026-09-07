<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspace\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\AccountantWorkspace\Enums\WorkspaceItemStatus;

final class WorkspaceItem extends Model
{
    protected $table = 'accounting_workspace_items';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'name', 'status', 'next_deadline', 'alerts', 'notes', 'requests', 'metadata'];

    protected $casts = ['status' => WorkspaceItemStatus::class, 'next_deadline' => 'datetime', 'alerts' => 'array', 'notes' => 'array', 'requests' => 'array', 'metadata' => 'array'];
}
