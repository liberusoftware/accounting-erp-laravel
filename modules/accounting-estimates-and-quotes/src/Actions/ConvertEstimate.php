<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Events\EstimateLifecycleChanged;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class ConvertEstimate
{
    public function handle(Estimate $e, string $convertedRef, ?string $actor = null): Estimate
    {
        if ($e->status !== EstimateStatus::Accepted || blank($convertedRef)) {
            throw new InvalidEstimate('Only accepted estimates can be converted and a target reference is required.');
        }if ($e->converted_ref !== null) {
            throw new InvalidEstimate('The estimate has already been converted.');
        }$e->update(['status' => EstimateStatus::Converted, 'converted_ref' => $convertedRef]);
        $e->history()->create(['event' => 'converted', 'actor_ref' => $actor, 'metadata' => ['converted_ref' => $convertedRef]]);
        $e = $e->refresh();
        event(new EstimateLifecycleChanged($e, 'converted', $actor));

        return $e;
    }
}
