<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Transfers\Enums\TransferStatus;
use Liberu\Accounting\Transfers\Exceptions\InvalidTransfer;
use Liberu\Accounting\Transfers\Models\Transfer;

final class CreateTransfer
{
    public function handle(array $attributes): Transfer
    {
        if (blank($attributes['source_account_ref'] ?? null) || blank($attributes['destination_account_ref'] ?? null) || $attributes['source_account_ref'] === $attributes['destination_account_ref'] || (float) ($attributes['source_amount'] ?? 0) <= 0 || (float) ($attributes['exchange_rate'] ?? 0) <= 0) {
            throw new InvalidTransfer('Transfer accounts must differ and amounts/rates must be positive.');
        }

        $attributes['destination_amount'] ??= round((float) $attributes['source_amount'] * (float) $attributes['exchange_rate'], 6);

        return DB::transaction(fn (): Transfer => Transfer::create(array_merge($attributes, ['status' => $attributes['status'] ?? TransferStatus::InTransit])));
    }
}
