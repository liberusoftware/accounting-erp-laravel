<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierPortal\Enums\PortalStatus;
use Liberu\Accounting\SupplierPortal\Events\PortalResourceStatusChanged;
use Liberu\Accounting\SupplierPortal\Exceptions\InvalidPortalResource;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;

final class TransitionPortalResource
{
    public function handle(PortalResource $resource, PortalStatus|string $status, ?string $reason = null): PortalResource
    {
        return DB::transaction(function () use ($resource, $status, $reason): PortalResource {
            $next = $status instanceof PortalStatus ? $status : PortalStatus::tryFrom($status);
            if (! $next) {
                throw new InvalidPortalResource('Unknown portal status.');
            }$allowed = [PortalStatus::Draft->value => [PortalStatus::Submitted, PortalStatus::Archived], PortalStatus::Submitted->value => [PortalStatus::UnderReview, PortalStatus::Rejected], PortalStatus::UnderReview->value => [PortalStatus::Approved, PortalStatus::Rejected, PortalStatus::Disputed], PortalStatus::Approved->value => [PortalStatus::Paid, PortalStatus::Disputed], PortalStatus::Disputed->value => [PortalStatus::UnderReview, PortalStatus::Approved], PortalStatus::Paid->value => [PortalStatus::Archived], PortalStatus::Rejected->value => [PortalStatus::Draft], PortalStatus::Archived->value => []];
            if (! in_array($next, $allowed[$resource->status->value], true)) {
                throw new InvalidPortalResource("Cannot transition {$resource->status->value} to {$next->value}.");
            }if ($next === PortalStatus::Rejected && blank($reason)) {
                throw new InvalidPortalResource('A rejection reason is required.');
            }$resource->update(['status' => $next, 'submitted_at' => $next === PortalStatus::Submitted ? now() : $resource->submitted_at, 'approved_at' => $next === PortalStatus::Approved ? now() : $resource->approved_at, 'rejected_reason' => $reason]);
            $resource = $resource->refresh();
            DB::afterCommit(fn () => event(new PortalResourceStatusChanged($resource)));

            return $resource;
        });
    }
}
