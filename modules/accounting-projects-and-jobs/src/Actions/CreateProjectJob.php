<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobs\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectsAndJobs\Enums\ProjectStatus;
use Liberu\Accounting\ProjectsAndJobs\Exceptions\InvalidProject;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

final class CreateProjectJob
{
    public function handle(array $attributes): ProjectJob
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        if ($name === '' || ($attributes['start_date'] ?? null) && ($attributes['end_date'] ?? null) && $attributes['end_date'] < $attributes['start_date']) {
            throw new InvalidProject('Name is required and end_date cannot precede start_date.');
        }

        return DB::transaction(fn (): ProjectJob => ProjectJob::create(['team_id' => $attributes['team_id'] ?? null, 'customer_id' => $attributes['customer_id'] ?? null, 'parent_id' => $attributes['parent_id'] ?? null, 'name' => $name, 'code' => $attributes['code'] ?? null, 'description' => $attributes['description'] ?? null, 'start_date' => $attributes['start_date'] ?? null, 'end_date' => $attributes['end_date'] ?? null, 'status' => ProjectStatus::Draft, 'manager_ref' => $attributes['manager_ref'] ?? null, 'budget_amount' => $attributes['budget_amount'] ?? null, 'budget_currency' => $attributes['budget_currency'] ?? null, 'dimensions' => $attributes['dimensions'] ?? [], 'source_links' => $attributes['source_links'] ?? [], 'metadata' => $attributes['metadata'] ?? null]));
    }
}
