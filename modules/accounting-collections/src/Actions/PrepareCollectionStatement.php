<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Actions;

use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class PrepareCollectionStatement
{
    public function handle(CollectionCase $case, array $statement): CollectionCase
    {
        if (blank($statement['period'] ?? null)) {
            throw new InvalidCollectionCase('Statement period is required.');
        }

        $case->update(['statement_run' => $statement]);

        return $case->refresh();
    }
}
