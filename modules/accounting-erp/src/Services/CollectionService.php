<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Carbon\Carbon;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Notifications\CollectionNotification;

class CollectionService
{
    public function flagDelinquentAccounts(): void
    {
        $customers = Customer::whereHas('invoices', function ($query): void {
            $query->where('payment_status', 'pending')
                ->where('due_date', '<', Carbon::now()->subDays(90));
        })->get();

        foreach ($customers as $customer) {
            $customer->credit_hold = true;
            $customer->save();

            if ($customer->email) {
                $customer->notify(new CollectionNotification($customer));
            }
        }
    }

    public function generateCollectionReport()
    {
        return Customer::where('credit_hold', true)
            ->with(['invoices' => function ($query): void {
                $query->where('payment_status', 'pending')
                    ->orderBy('due_date');
            }])
            ->get()
            ->map(fn ($customer): array => [
                'customer_name' => $customer->customer_name,
                'total_overdue' => $customer->invoices->sum('total_amount'),
                'oldest_invoice_date' => $customer->invoices->min('due_date'),
                'number_of_invoices' => $customer->invoices->count(),
                'contact_info' => [
                    'email' => $customer->customer_email,
                    'phone' => $customer->customer_phone,
                ],
            ]);
    }
}
