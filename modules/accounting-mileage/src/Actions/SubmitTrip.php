<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Liberu\Accounting\Mileage\Enums\TripStatus;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class SubmitTrip
{
    public function handle(MileageTrip $trip): MileageTrip
    {
        if ($trip->status !== TripStatus::Draft) {
            throw new InvalidMileage('Only draft trips can be submitted.');
        }if (blank($trip->business_purpose)) {
            throw new InvalidMileage('A business purpose is required before submission.');
        }$trip->update(['status' => TripStatus::Submitted, 'submitted_at' => now()]);

        return $trip->refresh();
    }
}
