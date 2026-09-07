<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Models\NumberingSequence;

final class AllocateNumber
{
    public function handle(NumberingSequence $sequence): string
    {
        return DB::transaction(function () use ($sequence): string {
            $locked = NumberingSequence::query()->lockForUpdate()->findOrFail($sequence->getKey());
            $number = $locked->prefix.str_pad((string) $locked->next_number, $locked->padding, '0', STR_PAD_LEFT);
            $locked->increment('next_number');

            return $number;
        });
    }
}
