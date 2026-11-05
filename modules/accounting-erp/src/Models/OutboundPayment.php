<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Erp\Concerns\Approvable;
use Liberu\Accounting\Erp\Contracts\ApprovableRecord;

/**
 * A bank payment instruction pending execution. Routed through the Approvable
 * maker-checker flow: below the ApprovalRule threshold it auto-approves and the
 * SendApprovedOutboundPayment listener fires it immediately; above threshold it
 * waits for a second approver before the same listener sends it.
 */
class OutboundPayment extends Model implements ApprovableRecord
{
    use Approvable;

    public const STATUS_PENDING_APPROVAL = 'pending_approval';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'team_id',
        'bank_connection_id',
        'requested_by',
        'account_id',
        'receiver',
        'amount',
        'currency',
        'reference',
        'idempotency_key',
        'status',
        'request_id',
        'result',
        'sent_at',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'receiver' => 'array',
        'result' => 'array',
        'amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function approvalAmount(): float
    {
        return (float) $this->amount;
    }

    public function bankConnection(): BelongsTo
    {
        return $this->belongsTo(BankConnection::class);
    }
}
