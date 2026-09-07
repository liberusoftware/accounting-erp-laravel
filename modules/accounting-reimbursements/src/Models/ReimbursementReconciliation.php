<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $batch_id @property float|string $expected_amount @property float|string $settled_amount @property float|string $variance @property string $status */
final class ReimbursementReconciliation extends Model
{
    protected $table = 'accounting_reimbursement_reconciliations';

    protected $fillable = ['batch_id', 'expected_amount', 'settled_amount', 'variance', 'status', 'external_ref', 'notes', 'metadata'];

    protected $casts = ['expected_amount' => 'decimal:2', 'settled_amount' => 'decimal:2', 'variance' => 'decimal:2', 'metadata' => 'array'];
}
