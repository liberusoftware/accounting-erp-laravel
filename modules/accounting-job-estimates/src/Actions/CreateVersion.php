<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\EstimateVersion;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class CreateVersion
{
    public function handle(JobEstimate $estimate, ?string $notes = null): EstimateVersion
    {
        if ($estimate->status === EstimateStatus::Archived) {
            throw new InvalidEstimate('Archived estimates cannot be versioned.');
        }

        return DB::transaction(function () use ($estimate, $notes): EstimateVersion {
            $number = (int) $estimate->version_no + 1;
            $version = EstimateVersion::create(['estimate_id' => $estimate->getKey(), 'version_no' => $number, 'status' => EstimateStatus::Draft, 'notes' => $notes]);
            foreach ($estimate->lines as $line) {
                $version->lines()->create(['estimate_id' => $estimate->getKey(), 'line_ref' => $line->line_ref, 'line_type' => $line->line_type, 'category' => $line->category, 'description' => $line->description, 'quantity' => $line->quantity, 'rate' => $line->rate, 'amount' => $line->amount, 'actual_amount' => $line->actual_amount, 'metadata' => $line->metadata]);
            }$estimate->update(['version_no' => $number, 'status' => EstimateStatus::Draft]);

            return $version->load('lines');
        });
    }
}
