<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

class domains extends Model
{
    use HasFactory;
    use IsTenantModel;
}
