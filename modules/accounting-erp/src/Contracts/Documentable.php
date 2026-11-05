<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Documentable
{
    public function documents(): MorphMany;
}
