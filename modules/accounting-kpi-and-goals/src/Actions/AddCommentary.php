<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\KpiAndGoals\Exceptions\InvalidKpi;
use Liberu\Accounting\KpiAndGoals\Models\KpiCommentary;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;

final class AddCommentary
{
    public function handle(KpiGoal $goal, string $actor, string $body, ?string $period = null): KpiCommentary
    {
        if (blank($body)) {
            throw new InvalidKpi('Commentary body is required.');
        }

        return DB::transaction(fn (): KpiCommentary => KpiCommentary::create(['goal_id' => $goal->id, 'actor_ref' => $actor, 'body' => $body, 'period_ref' => $period]));
    }
}
