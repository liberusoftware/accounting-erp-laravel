<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Leases\Models\Lease;
use Liberu\Accounting\Leases\Models\LeaseDisclosure;

final class CreateDisclosure
{
    public function handle(Lease $lease, string $asOfDate): LeaseDisclosure
    {
        $remaining = (float) $lease->lease_liability;
        $current = min($remaining, (float) $lease->payment_amount * 12);

        return DB::transaction(fn (): LeaseDisclosure => LeaseDisclosure::updateOrCreate(['lease_id' => $lease->id, 'as_of_date' => $asOfDate], ['remaining_liability' => $remaining, 'current_liability' => $current, 'non_current_liability' => max(0, $remaining - $current), 'future_payments' => $lease->payments()->where('status', 'scheduled')->get(['due_date', 'amount'])->toArray()]));
    }
}
