<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Events\SupplierBillPosted;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class PostSupplierBill
{
    public function __construct(private readonly CreateOpenItem $createOpenItem) {}

    public function handle(SupplierBill $bill): SupplierBill
    {
        return DB::transaction(function () use ($bill): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->id);
            if ($bill->status === SupplierBillStatus::Posted) {
                if (! PayableOpenItem::query()->where('source_type', SupplierBill::class)->where('source_id', (string) $bill->id)->exists()) {
                    $this->createOpenItem->handle(['party_id' => $bill->party_id, 'source_type' => SupplierBill::class, 'source_id' => (string) $bill->id, 'reference' => $bill->bill_number, 'issued_on' => $bill->bill_date, 'due_on' => $bill->due_on, 'original_amount' => $bill->total, 'currency' => $bill->currency, 'metadata' => ['supplier_bill_id' => $bill->id]]);
                }

                return $bill;
            }
            if ($bill->status !== SupplierBillStatus::Approved) {
                throw new InvalidSupplierBill('Only approved supplier bills may be posted.');
            }
            $this->createOpenItem->handle(['party_id' => $bill->party_id, 'source_type' => SupplierBill::class, 'source_id' => (string) $bill->id, 'reference' => $bill->bill_number, 'issued_on' => $bill->bill_date, 'due_on' => $bill->due_on, 'original_amount' => $bill->total, 'currency' => $bill->currency, 'metadata' => ['supplier_bill_id' => $bill->id]]);
            $bill->update(['status' => SupplierBillStatus::Posted]);
            $bill = $bill->refresh();
            DB::afterCommit(fn () => event(new SupplierBillPosted($bill)));

            return $bill;
        });
    }
}
