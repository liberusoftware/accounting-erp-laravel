<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $requester_ref
 * @property string $currency
 * @property float|string $total_amount
 * @property RequisitionStatus $status
 * @property array<string,mixed> $lines
 * @property array<string,mixed>|null $budget
 */
final class PurchaseRequisition extends Model
{
    protected $table = 'accounting_purchase_requisitions';

    protected $fillable = ['team_id', 'requester_ref', 'title', 'currency', 'total_amount', 'lines', 'coding', 'budget', 'attachments', 'status', 'submitted_at', 'approved_at', 'sourcing_ref', 'converted_ref', 'metadata'];

    protected $casts = ['status' => RequisitionStatus::class, 'total_amount' => 'decimal:2', 'lines' => 'array', 'coding' => 'array', 'budget' => 'array', 'attachments' => 'array', 'submitted_at' => 'datetime', 'approved_at' => 'datetime', 'metadata' => 'array'];

    public function approvals(): HasMany
    {
        return $this->hasMany(RequisitionApproval::class, 'requisition_id');
    }
}
