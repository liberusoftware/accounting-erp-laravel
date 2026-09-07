<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Actions;

use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;
use Liberu\Accounting\JournalApprovals\Models\JournalEvidence;

final class AddEvidence
{
    public function handle(JournalApproval $approval, array $attributes): JournalEvidence
    {
        if (blank($attributes['kind'] ?? null)) {
            throw new InvalidApproval('Evidence kind is required.');
        }

        return JournalEvidence::create([
            'approval_id' => $approval->getKey(),
            ...$attributes,
        ]);
    }
}
