<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesTeams;
use Liberu\Accounting\Erp\Models\Team;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team): void
    {
        $team->purge();
    }
}
