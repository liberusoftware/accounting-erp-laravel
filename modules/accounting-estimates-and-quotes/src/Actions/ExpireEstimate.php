<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Carbon\CarbonImmutable;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Events\EstimateLifecycleChanged;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class ExpireEstimate
{
    public function handle(Estimate $e, ?string $actor = null): Estimate
    {
        if (! in_array($e->status, [EstimateStatus::Sent, EstimateStatus::Draft], true) || ! $e->expires_on || CarbonImmutable::parse($e->expires_on->toDateString())->isFuture()) {
            throw new InvalidEstimate('Only an expired draft or sent estimate can be expired.');
        }$e->update(['status' => EstimateStatus::Expired]);
        $e->history()->create(['event' => 'expired', 'actor_ref' => $actor]);
        $e = $e->refresh();
        event(new EstimateLifecycleChanged($e, 'expired', $actor));

        return $e;
    }
}
