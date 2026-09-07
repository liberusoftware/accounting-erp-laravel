<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Policies;

use Liberu\Accounting\FixedAssets\Models\Asset;

final class AssetPolicy
{
    public function view(mixed $user, Asset $asset): bool
    {
        return $this->sameTeam($user, $asset->team_id);
    }

    public function create(mixed $user): bool
    {
        return $user !== null && isset($user->current_team_id);
    }

    public function update(mixed $user, Asset $asset): bool
    {
        return $this->sameTeam($user, $asset->team_id);
    }

    public function capitalize(mixed $user, Asset $asset): bool
    {
        return $this->sameTeam($user, $asset->team_id);
    }

    public function dispose(mixed $user, Asset $asset): bool
    {
        return $this->sameTeam($user, $asset->team_id);
    }

    private function sameTeam(mixed $user, ?int $teamId): bool
    {
        return $user !== null && isset($user->current_team_id) && $teamId !== null
            && (int) $user->current_team_id === $teamId;
    }
}
