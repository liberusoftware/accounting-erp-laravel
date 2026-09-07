<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Events\EstimateLifecycleChanged;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class SendEstimate
{
    public function handle(Estimate $e, ?string $actor = null): Estimate
    {
        if ($e->status !== EstimateStatus::Draft || $e->items()->count() === 0) {
            throw new InvalidEstimate('Only draft estimates with items can be sent.');
        }$e->update(['status' => EstimateStatus::Sent]);
        $e->history()->create(['event' => 'sent', 'actor_ref' => $actor]);
        $e = $e->refresh();
        event(new EstimateLifecycleChanged($e, 'sent', $actor));

        return $e;
    }
}
