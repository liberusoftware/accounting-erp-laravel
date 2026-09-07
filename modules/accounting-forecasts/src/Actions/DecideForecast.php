<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Events\ForecastApproved;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;

final class DecideForecast
{
    public function handle(Forecast $forecast, string $actor, bool $approved, ?string $comment = null): Forecast
    {
        if ($forecast->status !== ForecastStatus::Submitted) {
            throw new InvalidForecast('Only submitted forecasts can be approved.');
        }
        if (! $approved && blank($comment)) {
            throw new InvalidForecast('Rejected forecasts require a comment.');
        }
        $forecast->approvals()->create(['actor_ref' => $actor, 'approved' => $approved, 'comment' => $comment, 'decided_at' => now()]);
        $forecast->update(['status' => $approved ? ForecastStatus::Approved : ForecastStatus::Draft]);
        $forecast = $forecast->refresh();
        if ($approved) {
            event(new ForecastApproved($forecast, $actor));
        }

        return $forecast;
    }
}
