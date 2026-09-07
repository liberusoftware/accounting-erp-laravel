<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Actions;

use Liberu\Accounting\GeneralLedger\Enums\JournalType;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;

final class CreateTypedJournal
{
    public function handle(JournalType $type, array $attributes, array $lines): JournalEntry
    {
        return app(CreateJournal::class)->handle($attributes + ['journal_type' => $type->value], $lines);
    }
}
