<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Models;

use Illuminate\Database\Eloquent\Model;

/** @property array<string,mixed> $dimensions */
class DimensionAllocation extends Model
{
    protected $table = 'accounting_dimension_allocations';

    protected $fillable = ['allocation_key', 'amount', 'currency', 'percentage', 'dimensions', 'created_by'];

    protected $casts = ['amount' => 'decimal:2', 'percentage' => 'decimal:4', 'dimensions' => 'array'];
}
