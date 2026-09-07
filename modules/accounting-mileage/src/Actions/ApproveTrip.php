<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Enums\ApprovalDecision;
use Liberu\Accounting\Mileage\Enums\TripStatus;
use Liberu\Accounting\Mileage\Events\TripApproved;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class ApproveTrip
{
    public function handle(MileageTrip $trip, string $actor, bool $approved = true, ?string $reason = null): MileageTrip
    {
        if ($trip->status !== TripStatus::Submitted) {
            throw new InvalidMileage('Only submitted trips can be approved or rejected.');
        }if (! $approved && blank($reason)) {
            throw new InvalidMileage('A rejection requires a reason.');
        }

        return DB::transaction(function () use ($trip, $actor, $approved, $reason): MileageTrip {
            $decision = $approved ? ApprovalDecision::Approved : ApprovalDecision::Rejected;
            $trip->approvals()->create(['actor_ref' => $actor, 'decision' => $decision, 'reason' => $reason, 'decided_at' => now()]);
            $trip->update(['status' => $approved ? TripStatus::Approved : TripStatus::Rejected, 'approved_at' => $approved ? now() : null]);
            $result = $trip->refresh();
            if ($approved) {
                DB::afterCommit(fn () => event(new TripApproved($result)));
            }

            return $result;
        });
    }
}
