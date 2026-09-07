<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\AuditSupport\Enums\AuditRequestStatus;

final class AuditRequest extends Model
{
    protected $table = 'accounting_audit_requests';

    protected $fillable = ['team_id', 'reference', 'title', 'description', 'owner_id', 'status', 'due_at', 'evidence', 'submitted_at', 'closed_at'];

    protected $casts = ['status' => AuditRequestStatus::class, 'evidence' => 'array', 'owner_id' => 'integer', 'due_at' => 'datetime', 'submitted_at' => 'datetime', 'closed_at' => 'datetime'];
}
