<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Traits;

use Liberu\Foundation\Tenancy\Concerns\BelongsToTeam;

trait IsTenantModel
{
    use BelongsToTeam;
}
