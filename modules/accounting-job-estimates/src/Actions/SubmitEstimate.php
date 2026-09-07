<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class SubmitEstimate
{
    public function handle(JobEstimate $estimate): JobEstimate
    {
        if ($estimate->status !== EstimateStatus::Draft && $estimate->status !== EstimateStatus::Rejected) {
            throw new InvalidEstimate('Only draft or rejected estimates can be submitted.');
        }if ($estimate->lines()->count() === 0) {
            throw new InvalidEstimate('An estimate requires at least one line.');
        }$estimate->update(['status' => EstimateStatus::Submitted]);

        return $estimate->refresh();
    }
}
