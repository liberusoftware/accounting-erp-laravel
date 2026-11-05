<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ConsolidationGroup extends Model
{
    #[\Override]
    protected $fillable = [
        'name',
        'owner_team_id',
    ];

    public function ownerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'owner_team_id');
    }

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'consolidation_group_team');
    }
}
