<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Actions;

use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class IssueConstructionTaxStatement
{
    public function handle(ConstructionTaxRecord $record, array $statement): ConstructionTaxRecord
    {
        if (blank($statement['reference'] ?? null)) {
            throw new InvalidConstructionTax('A statement reference is required.');
        }

        $record->update(['statement' => $statement]);

        return $record->refresh();
    }
}
