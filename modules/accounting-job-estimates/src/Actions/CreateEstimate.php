<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class CreateEstimate
{
    public function handle(array $attributes): JobEstimate
    {
        $ref = trim((string) ($attributes['estimate_ref'] ?? ''));
        foreach (['project_ref', 'title', 'currency'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidEstimate("Missing estimate field [{$key}].");
            }
        }if ($ref === '') {
            throw new InvalidEstimate('Estimate reference is required.');
        }

        return DB::transaction(fn (): JobEstimate => JobEstimate::create(['team_id' => $attributes['team_id'] ?? null, 'estimate_ref' => $ref, 'project_ref' => $attributes['project_ref'], 'title' => $attributes['title'], 'currency' => strtoupper($attributes['currency']), 'status' => EstimateStatus::Draft, 'version_no' => 1, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
