<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Actions;

use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class SendToCollectionAgency
{
    public function handle(CollectionCase $case, string $agency): CollectionCase
    {
        if (blank($agency)) {
            throw new InvalidCollectionCase('Agency adapter is required.');
        }

        $case->update(['agency' => ['adapter' => $agency, 'sent_at' => now()->toIso8601String()], 'stage' => 'agency']);

        return $case->refresh();
    }
}
