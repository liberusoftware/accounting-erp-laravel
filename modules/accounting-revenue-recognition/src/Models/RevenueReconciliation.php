<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $run_id @property string $reference_type @property string $reference_id @property float|string $expected_amount @property float|string $recognized_amount @property float|string $variance @property string $status */
final class RevenueReconciliation extends Model
{
    protected $table = 'accounting_revenue_reconciliations';

    protected $fillable = ['run_id', 'reference_type', 'reference_id', 'expected_amount', 'recognized_amount', 'variance', 'status', 'notes', 'metadata'];

    protected $casts = ['expected_amount' => 'decimal:2', 'recognized_amount' => 'decimal:2', 'variance' => 'decimal:2', 'metadata' => 'array'];
}
