<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Actions;

use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class ScheduleCollectionReminder
{
    public function handle(CashCollectionAssistant $assistant, string $at, ?string $draft = null): CashCollectionAssistant
    {
        $assistant->forceFill(['reminder_at' => $at, 'reminder_status' => 'scheduled', 'metadata' => [...($assistant->metadata ?? []), 'reminder_draft' => $draft]])->save();

        return $assistant->refresh();
    }
}
