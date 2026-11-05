<?php

// src/app/Models/Subscription.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

/**
 * @property-read Plan|null $plan
 */
class Subscription extends Model
{
    use HasFactory;
    use IsTenantModel;

    #[\Override]
    protected $fillable = [
        'customer_id', 'plan_id', 'status', 'started_at', 'next_billing_date', 'last_billed_at', 'cancelled_at', 'team_id',
    ];

    #[\Override]
    protected $casts = [
        'started_at' => 'date',
        'next_billing_date' => 'date',
        'last_billed_at' => 'date',
        'cancelled_at' => 'datetime',
    ];

    #[\Override]
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Subscription $subscription): void {
            if (empty($subscription->team_id) && ($team = auth()->user()?->currentTeam) !== null) {
                $subscription->team_id = $team->getKey();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        $next = $this->next_billing_date;
        if ($next !== null && $next->isPast()) {
            $next = today();
        }
        $this->update(['status' => 'active', 'next_billing_date' => $next]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled', 'cancelled_at' => now()]);
    }
}
