<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Actions;

use Liberu\Accounting\GeneralLedger\Enums\JournalType;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;

final class CreatePrepayment
{
    public function handle(array $attributes, array $lines): JournalEntry
    {
        return app(CreateTypedJournal::class)->handle(JournalType::Prepayment, $attributes, $lines);
    }
}
