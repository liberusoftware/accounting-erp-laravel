<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Enums\TripStatus;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\MileageReimbursement;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class ReimburseTrip
{
    public function handle(MileageTrip $trip, string $payee, ?string $externalRef = null): MileageReimbursement
    {
        if ($trip->status !== TripStatus::Approved) {
            throw new InvalidMileage('Only approved trips can be reimbursed.');
        }if ($trip->reimbursements()->where('payee_ref', $payee)->exists()) {
            throw new InvalidMileage('This trip is already reimbursed for the payee.');
        }

        return DB::transaction(function () use ($trip, $payee, $externalRef): MileageReimbursement {
            $reimbursement = MileageReimbursement::create(['trip_id' => $trip->id, 'payee_ref' => $payee, 'currency' => $trip->currency, 'amount' => $trip->reimbursement_amount, 'status' => 'submitted', 'external_ref' => $externalRef]);
            $trip->update(['status' => TripStatus::Reimbursed, 'reimbursed_at' => now()]);

            return $reimbursement;
        });
    }
}
