<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection\Actions;

use Carbon\CarbonImmutable;
use Liberu\Accounting\AnomalyDetection\Enums\AnomalyStatus;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;

final class RecordAnomaly
{
    public function handle(array $attributes): Anomaly
    {
        foreach (['team_id', 'kind', 'title'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        }

        return Anomaly::create([...$attributes, 'status' => AnomalyStatus::Open, 'detected_at' => $attributes['detected_at'] ?? CarbonImmutable::now()]);
    }
}
