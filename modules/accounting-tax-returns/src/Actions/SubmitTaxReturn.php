<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TaxReturns\Enums\TaxReturnStatus;
use Liberu\Accounting\TaxReturns\Exceptions\InvalidTaxReturn;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;

final class SubmitTaxReturn
{
    public function handle(TaxReturn $taxReturn, string $externalReference): TaxReturn
    {
        if ($taxReturn->status !== TaxReturnStatus::Draft && $taxReturn->status !== TaxReturnStatus::Ready) {
            throw new InvalidTaxReturn('Only draft or ready returns can be submitted.');
        }

        return DB::transaction(function () use ($taxReturn, $externalReference): TaxReturn {
            $taxReturn->update(['status' => TaxReturnStatus::Submitted, 'external_reference' => $externalReference, 'submitted_at' => now()]);

            return $taxReturn->refresh();
        });
    }
}
