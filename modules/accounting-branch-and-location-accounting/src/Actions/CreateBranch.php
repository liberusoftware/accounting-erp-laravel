<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BranchLocationAccounting\Models\Branch;

final class CreateBranch
{
    public function handle(array $attributes): Branch
    {
        abort_if(blank($attributes['team_id'] ?? null) || blank($attributes['code'] ?? null) || blank($attributes['name'] ?? null), 422, 'Team, code and name are required.');

        return DB::transaction(fn (): Branch => Branch::create([...$attributes, 'status' => $attributes['status'] ?? 'active']));
    }
}
