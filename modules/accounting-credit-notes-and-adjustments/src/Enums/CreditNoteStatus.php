<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Enums;

enum CreditNoteStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case PartiallyAllocated = 'partially_allocated';
    case Allocated = 'allocated';
    case Refunded = 'refunded';
}
