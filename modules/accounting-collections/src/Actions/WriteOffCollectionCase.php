<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Actions;

use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class WriteOffCollectionCase
{
    public function handle(CollectionCase $case, array $writeOff): CollectionCase
    {
        if (blank($writeOff['reason'] ?? null) || ! isset($writeOff['amount']) || (float) $writeOff['amount'] <= 0) {
            throw new InvalidCollectionCase('Write-off reason and positive amount are required.');
        }

        $case->update(['write_off' => $writeOff, 'stage' => 'written-off']);

        return $case->refresh();
    }
}
