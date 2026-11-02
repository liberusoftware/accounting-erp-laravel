<?php

declare(strict_types=1);

namespace App\Traits;

use Liberu\Foundation\Tenancy\Concerns\BelongsToTeam;

trait IsTenantModel
{
    use BelongsToTeam;
}
