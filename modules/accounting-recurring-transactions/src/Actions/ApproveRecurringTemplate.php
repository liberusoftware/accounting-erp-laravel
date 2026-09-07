<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RecurringTransactions\Enums\RecurringStatus;
use Liberu\Accounting\RecurringTransactions\Exceptions\InvalidRecurringTransaction;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;

final class ApproveRecurringTemplate
{
    public function handle(RecurringTemplate $template, ?int $actorId = null): RecurringTemplate
    {
        if ($template->status !== RecurringStatus::Draft && $template->status !== RecurringStatus::PendingApproval) {
            throw new InvalidRecurringTransaction('Only draft recurring templates can be approved.');
        }

        return DB::transaction(fn (): RecurringTemplate => tap($template)->update(['status' => RecurringStatus::Active, 'approved_by' => $actorId, 'approved_at' => now()]));
    }
}
