<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Liberu\Accounting\Erp\Contracts\ApprovableRecord;

/**
 * @property string $status
 * @property int $team_id
 * @property int $current_step
 * @property-read ApprovableRecord|null $approvable
 * @property-read ApprovalRule|null $rule
 */
class ApprovalRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    #[\Override]
    protected $fillable = ['team_id', 'approvable_type', 'approvable_id', 'rule_id', 'status', 'current_step'];

    #[\Override]
    protected $casts = ['current_step' => 'integer'];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<ApprovalStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('position');
    }

    /**
     * @return BelongsTo<ApprovalRule, $this>
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(ApprovalRule::class, 'rule_id');
    }
}
