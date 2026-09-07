<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Models\VatDigitalRecord;
use Liberu\Accounting\Vat\Models\VatRecord;

final class RecordVatDigitalEvidence
{
    public function handle(VatRecord $vatRecord, array $payload): VatDigitalRecord
    {
        if ($payload === []) {
            throw new InvalidVat('Digital VAT evidence cannot be empty.');
        }

        $encoded = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        return DB::transaction(fn (): VatDigitalRecord => $vatRecord->digitalRecord()->updateOrCreate([], ['record_hash' => hash('sha256', $encoded), 'payload' => $payload, 'recorded_at' => now()]));
    }
}
