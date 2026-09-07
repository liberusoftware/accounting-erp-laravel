<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Exceptions\InvalidRequisition;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;

final class TransitionRequisition
{
    public function handle(PurchaseRequisition $requisition, RequisitionStatus $status, array $attributes = []): PurchaseRequisition
    {
        $valid = match ($requisition->status) {
            RequisitionStatus::Draft => [RequisitionStatus::Submitted, RequisitionStatus::Cancelled],RequisitionStatus::Submitted => [RequisitionStatus::Approved, RequisitionStatus::Rejected],RequisitionStatus::Approved => [RequisitionStatus::Sourcing, RequisitionStatus::Converted],RequisitionStatus::Sourcing => [RequisitionStatus::Converted, RequisitionStatus::Cancelled],default => []
        };
        if (! in_array($status, $valid, true)) {
            throw new InvalidRequisition("Cannot transition from {$requisition->status->value} to {$status->value}.");
        }

        return DB::transaction(function () use ($requisition, $status, $attributes): PurchaseRequisition {
            $data = ['status' => $status];
            if ($status === RequisitionStatus::Submitted) {
                $data['submitted_at'] = now();
            }if ($status === RequisitionStatus::Approved) {
                $data['approved_at'] = now();
            }if ($status === RequisitionStatus::Sourcing) {
                $data['sourcing_ref'] = $attributes['sourcing_ref'] ?? null;
            }if ($status === RequisitionStatus::Converted) {
                $data['converted_ref'] = $attributes['converted_ref'] ?? null;
            }$requisition->update($data);

            return $requisition->refresh();
        });
    }
}
