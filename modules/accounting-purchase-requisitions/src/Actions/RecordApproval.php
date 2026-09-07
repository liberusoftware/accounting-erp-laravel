<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Exceptions\InvalidRequisition;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;
use Liberu\Accounting\PurchaseRequisitions\Models\RequisitionApproval;

final class RecordApproval
{
    public function handle(PurchaseRequisition $requisition, array $attributes): RequisitionApproval
    {
        if ($requisition->status !== RequisitionStatus::Submitted || blank($attributes['approver_ref'] ?? null)) {
            throw new InvalidRequisition('Only submitted requisitions can be approved.');
        }$decision = $attributes['decision'] ?? null;
        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw new InvalidRequisition('Approval decision is invalid.');
        }

        return DB::transaction(function () use ($requisition, $attributes, $decision): RequisitionApproval {
            $approval = RequisitionApproval::create(['requisition_id' => $requisition->id, 'approver_ref' => $attributes['approver_ref'], 'decision' => $decision, 'reason' => $attributes['reason'] ?? null, 'decided_at' => now()]);
            $requisition->update(['status' => $decision === 'approved' ? RequisitionStatus::Approved : RequisitionStatus::Rejected, 'approved_at' => $decision === 'approved' ? now() : null]);

            return $approval;
        });
    }
}
