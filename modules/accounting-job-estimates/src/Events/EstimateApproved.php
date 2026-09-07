<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class EstimateApproved
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly JobEstimate $estimate) {}
}
