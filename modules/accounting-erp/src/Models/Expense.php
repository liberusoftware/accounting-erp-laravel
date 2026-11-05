<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Erp\Concerns\Approvable;
use Liberu\Accounting\Erp\Concerns\Recurring;
use Liberu\Accounting\Erp\Contracts\ApprovableRecord;
use Liberu\Accounting\Erp\Notifications\ExpenseApprovalNotification;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

/**
 * @property int|null $team_id
 * @property float|string $amount
 * @property string|null $description
 * @property bool $is_indirect
 * @property string|null $approval_status
 * @property int|null $approved_by
 * @property-read User|null $user
 */
class Expense extends Model implements ApprovableRecord
{
    use Approvable;
    use IsTenantModel;
    use Recurring;

    #[\Override]
    protected $fillable = [
        'amount',
        'description',
        'date',
        'approval_status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'project_id',
        'is_indirect',
        'allocation_percentage',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_start',
        'recurrence_end',
        'last_generated',
        'user_id',
        'team_id',
    ];

    #[\Override]
    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_indirect' => 'boolean',
        'allocation_percentage' => 'decimal:2',
        'is_recurring' => 'boolean',
        'recurrence_start' => 'date',
        'recurrence_end' => 'date',
        'last_generated' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approve(): void
    {
        $this->markApproved();

        $this->user->notify(new ExpenseApprovalNotification($this, 'approved'));
    }

    public function reject(?string $reason): void
    {
        $this->markRejected($reason);

        $this->user->notify(new ExpenseApprovalNotification($this, 'rejected'));
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function getAllocatedAmount(): float
    {
        if ($this->is_indirect) {
            return $this->amount * ($this->allocation_percentage / 100);
        }

        return $this->amount;
    }

    // Approval threshold (Liberu\Accounting\Erp\Concerns\Approvable): an expense is routed on its amount.
    public function approvalAmount(): float
    {
        return (float) $this->amount;
    }

    // Recurrence hooks (Liberu\Accounting\Erp\Concerns\Recurring). No number column, no line
    // items -> the trait's null defaults are correct; only these two differ.

    /**
     * @return array<string, mixed>
     */
    protected function recurringDraftAttributes(): array
    {
        return ['approval_status' => 'pending', 'approved_by' => null, 'approved_at' => null, 'rejection_reason' => null];
    }

    protected function recurringDateColumns(Carbon $date): array
    {
        return ['date' => $date->copy()];
    }
}
