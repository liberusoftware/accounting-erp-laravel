<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Events\EstimateLifecycleChanged;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class DecideEstimate
{
    public function handle(Estimate $e, bool $accepted, ?string $reason = null, ?string $actor = null): Estimate
    {
        if ($e->status !== EstimateStatus::Sent) {
            throw new InvalidEstimate('Only sent estimates can be accepted or declined.');
        }if (! $accepted && blank($reason)) {
            throw new InvalidEstimate('A decline reason is required.');
        }$e->update(['status' => $accepted ? EstimateStatus::Accepted : EstimateStatus::Declined, 'accepted_at' => $accepted ? now() : null, 'declined_reason' => $accepted ? null : $reason]);
        $event = $accepted ? 'accepted' : 'declined';
        $e->history()->create(['event' => $event, 'actor_ref' => $actor, 'metadata' => $reason ? ['reason' => $reason] : null]);
        $e = $e->refresh();
        event(new EstimateLifecycleChanged($e, $event, $actor));

        return $e;
    }
}
