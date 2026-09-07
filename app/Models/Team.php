<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

/**
 * @property int $user_id
 * @property string $name
 * @property string|null $vonage_from
 * @property string|null $vonage_key
 * @property string|null $vonage_secret
 * @property array<string, mixed>|null $accounting_setup
 * @property Carbon|null $accounting_setup_completed_at
 * @property string|null $stripe_customer_id
 * @property string|null $stripe_subscription_id
 * @property string|null $stripe_product_id
 * @property string|null $stripe_price_id
 * @property string|null $premium_status
 * @property Carbon|null $premium_trial_ends_at
 * @property Carbon|null $premium_current_period_ends_at
 * @property string|null $premium_last_event_id
 * @property-read Carbon|null $books_locked_before
 */
class Team extends JetstreamTeam
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'name',
        'personal_team',
        'books_locked_before',
        'vonage_key',
        'vonage_secret',
        'vonage_from',
        'accounting_setup',
        'accounting_setup_completed_at',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_product_id',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'stripe_price_id',
        'premium_status',
        'premium_trial_ends_at',
        'premium_current_period_ends_at',
        'premium_cancel_at_period_end',
        'premium_last_event_id',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    #[\Override]
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'books_locked_before' => 'date',
            'vonage_key' => 'encrypted',
            'vonage_secret' => 'encrypted',
            'premium_trial_ends_at' => 'datetime',
            'premium_current_period_ends_at' => 'datetime',
            'premium_cancel_at_period_end' => 'boolean',
            'accounting_setup' => 'encrypted:array',
            'accounting_setup_completed_at' => 'datetime',
        ];
    }

    public function hasPremiumAccess(?Carbon $at = null): bool
    {
        if (! config('premium.enabled', false)) {
            return false;
        }

        $at ??= now();

        return in_array($this->premium_status, ['trialing', 'active'], true)
            && ($this->premium_status === 'trialing'
                ? $this->premium_trial_ends_at === null || $this->premium_trial_ends_at->greaterThanOrEqualTo($at)
                : $this->premium_current_period_ends_at === null || $this->premium_current_period_ends_at->greaterThanOrEqualTo($at));
    }

    public function isPremiumTrial(): bool
    {
        return $this->premium_status === 'trialing'
            && ($this->premium_trial_ends_at === null || ! $this->premium_trial_ends_at->isPast());
    }

    /**
     * The user who owns the team.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Users who belong to the team.
     *
     * @return BelongsToMany<User, $this, Pivot, 'membership'>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Pending invitations for the team.
     *
     * @return HasMany<TeamInvitation, $this>
     */
    public function teamInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
