<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

class TeamInvitation extends JetstreamTeamInvitation
{
    use IsTenantModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    #[\Override]
    protected $fillable = [
        'email',
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    #[\Override]
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
