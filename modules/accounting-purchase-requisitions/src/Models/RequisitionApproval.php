<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $requisition_id @property string $approver_ref @property string $decision @property string|null $reason */
final class RequisitionApproval extends Model
{
    protected $table = 'accounting_requisition_approvals';

    protected $fillable = ['requisition_id', 'approver_ref', 'decision', 'reason', 'decided_at', 'metadata'];

    protected $casts = ['decided_at' => 'datetime', 'metadata' => 'array'];
}
