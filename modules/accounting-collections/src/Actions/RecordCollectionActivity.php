<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Actions;

use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class RecordCollectionActivity
{
    public function reminder(CollectionCase $case, array $reminder): CollectionCase
    {
        if (blank($reminder['scheduled_for'] ?? null)) {
            throw new InvalidCollectionCase('Reminder schedule is required.');
        }

        $case->update(['reminders' => [...($case->reminders ?? []), $reminder]]);

        return $case->refresh();
    }

    public function promise(CollectionCase $case, array $promise): CollectionCase
    {
        if (blank($promise['due_on'] ?? null) || ! isset($promise['amount'])) {
            throw new InvalidCollectionCase('Promise due date and amount are required.');
        }

        $case->update(['promise_to_pay' => $promise, 'stage' => 'promise-to-pay']);

        return $case->refresh();
    }

    public function dispute(CollectionCase $case, array $dispute): CollectionCase
    {
        if (blank($dispute['reason'] ?? null)) {
            throw new InvalidCollectionCase('Dispute reason is required.');
        }

        $case->update(['disputes' => [...($case->disputes ?? []), $dispute], 'stage' => 'dispute']);

        return $case->refresh();
    }
}
