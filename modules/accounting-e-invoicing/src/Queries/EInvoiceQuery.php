<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class EInvoiceQuery
{
    public function paginate(?int $legalEntityId = null, ?DocumentStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return EInvoiceDocument::query()->when($legalEntityId !== null, fn ($q) => $q->where('legal_entity_id', $legalEntityId))->when($status !== null, fn ($q) => $q->where('status', $status))->with('events')->latest()->paginate(min(max($perPage, 1), 100));
    }
}
