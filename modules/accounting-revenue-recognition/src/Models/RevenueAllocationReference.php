<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int|null $team_id @property string $reference_type @property string $reference_id @property array<string,mixed> $allocation @property string $status */
final class RevenueAllocationReference extends Model
{
    protected $table = 'accounting_revenue_allocation_references';

    protected $fillable = ['team_id', 'reference_type', 'reference_id', 'allocation', 'status', 'metadata'];

    protected $casts = ['allocation' => 'array', 'metadata' => 'array'];
}
