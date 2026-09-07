<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptLine;

final class ReceiptQuery
{
    public function paginate(?int $teamId = null, ?ReceiptStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Receipt::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['lines', 'confirmations', 'returns', 'attachments', 'accruals'])->latest('received_at')->paginate(min(max($perPage, 1), 100));
    }

    public function variance(Receipt $receipt): array
    {
        $receipt->load('lines');

        return ['quantity_variance' => (float) $receipt->lines->sum('variance_quantity'), 'value_variance' => (float) $receipt->lines->sum('variance_value'), 'lines' => $receipt->lines->map(fn (ReceiptLine $line) => ['line_ref' => $line->line_ref, 'ordered' => $line->ordered_quantity, 'received' => $line->received_quantity, 'returned' => $line->returned_quantity, 'variance' => $line->variance_quantity])->values()->all()];
    }
}
