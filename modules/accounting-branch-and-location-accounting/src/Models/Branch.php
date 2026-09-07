<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccounting\Models;

use Illuminate\Database\Eloquent\Model;

final class Branch extends Model
{
    protected $table = 'accounting_branches';

    protected $fillable = ['team_id', 'code', 'name', 'location', 'local_tax_code', 'sequence_prefix', 'allocation_rule', 'performance_target', 'statutory_reference', 'status', 'metadata'];

    protected $casts = ['performance_target' => 'decimal:8', 'metadata' => 'array'];
}
