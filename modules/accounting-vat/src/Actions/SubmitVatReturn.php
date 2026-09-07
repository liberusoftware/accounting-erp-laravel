<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Vat\Enums\VatReturnStatus;
use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Models\VatReturn;

final class SubmitVatReturn
{
    public function handle(VatReturn $vatReturn, ?string $submissionRef = null): VatReturn
    {
        if ($vatReturn->status !== VatReturnStatus::Draft) {
            throw new InvalidVat('Only draft VAT returns can be submitted.');
        }

        return DB::transaction(function () use ($vatReturn, $submissionRef): VatReturn {
            $vatReturn->update(['status' => VatReturnStatus::Submitted, 'submitted_at' => now(), 'submission_ref' => $submissionRef]);

            return $vatReturn->fresh();
        });
    }
}
