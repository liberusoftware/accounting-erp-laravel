<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class TripCaptured
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly MileageTrip $trip) {}
}
