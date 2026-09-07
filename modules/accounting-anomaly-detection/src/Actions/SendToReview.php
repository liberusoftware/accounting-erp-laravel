<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection\Actions;

use Carbon\CarbonImmutable;
use Liberu\Accounting\AnomalyDetection\Enums\AnomalyStatus;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;

final class SendToReview
{
    public function handle(Anomaly $anomaly): Anomaly
    {
        if ($anomaly->status !== AnomalyStatus::Open) {
            throw new \InvalidArgumentException('Only open anomalies may be sent to review.');
        }

        return tap($anomaly)->update(['status' => AnomalyStatus::SentToReview, 'resolved_at' => CarbonImmutable::now()]);
    }
}
