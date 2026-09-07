<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\Vat\Models\VatRecord;
use Liberu\Accounting\Vat\Models\VatReturn;

final class VatReturnBoxes
{
    /** @return array<int, string> */
    public function handle(VatReturn $vatReturn): array
    {
        $records = VatRecord::query()->where('team_id', $vatReturn->team_id)->whereBetween('occurred_on', [$vatReturn->period_start, $vatReturn->period_end])->where('scheme', $vatReturn->scheme)->get();
        $boxes = $records->groupBy('box')->map(fn (Collection $rows): string => (string) $rows->sum('tax_amount'))->all();

        return array_replace($boxes, $vatReturn->boxes ?? []);
    }
}
