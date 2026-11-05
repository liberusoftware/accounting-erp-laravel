<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\SalesOrder;

class SalesOrderService
{
    public function createFromEstimate(Estimate $estimate): SalesOrder
    {
        if ($estimate->status !== 'accepted') {
            throw new \DomainException('Only an accepted estimate can become a sales order.');
        }
        if ($estimate->salesOrder()->exists()) {
            throw new \DomainException('This estimate already has a sales order.');
        }
        // The legacy direct estimate->invoice path (Estimate::convertToInvoice) sets
        // invoice_id; block the SO path too, or one estimate ends up with two invoices.
        if ($estimate->invoice_id !== null) {
            throw new \DomainException('This estimate has already been invoiced directly.');
        }

        return DB::transaction(function () use ($estimate): SalesOrder {
            $order = SalesOrder::create([
                'customer_id' => $estimate->customer_id,
                'estimate_id' => $estimate->estimate_id,
                'order_date' => today(),
                'status' => 'confirmed',
                'subtotal_amount' => $estimate->subtotal_amount,
                'tax_amount' => $estimate->tax_amount,
                'total_amount' => $estimate->total_amount,
            ]);

            foreach ($estimate->items as $item) {
                $order->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'amount' => $item->amount,
                    'tax_amount' => $item->tax_amount,
                    'tax_rate_id' => $item->tax_rate_id,
                ]);
            }

            return $order;
        });
    }

    public function convertToInvoice(SalesOrder $order): Invoice
    {
        if (in_array($order->status, ['invoiced', 'cancelled'], true)) {
            throw new \DomainException('This sales order cannot be invoiced.');
        }
        if ($order->invoice()->exists()) {
            throw new \DomainException('This sales order already has an invoice.');
        }

        return DB::transaction(function () use ($order): Invoice {
            $invoice = Invoice::create([
                'customer_id' => $order->customer_id,
                'sales_order_id' => $order->id,
                // Invoice has no team-stamping hook (unlike SalesOrder); pass it
                // explicitly or the row falls back to the team_id=1 column default.
                'team_id' => $order->team_id,
                'invoice_date' => today(),
                'due_date' => today()->addDays(30),
                // App convention: invoices.total_amount is the PRE-TAX subtotal; line
                // tax lives per-item (getTotalWithTax() sums it). Seeding the SO's
                // tax-inclusive total would be clobbered by InvoiceItem's saved hook
                // (calculateTotals = sum of line amounts) — so seed the subtotal.
                'total_amount' => $order->subtotal_amount,
                'payment_status' => 'pending',
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create([
                    // account_id rides along from the estimate; estimates carry none,
                    // so it is null and must be assigned before GL posting (see the
                    // pre-existing estimate->invoice path — same limitation).
                    'account_id' => $item->account_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'amount' => $item->amount,
                    'tax_amount' => $item->tax_amount,
                    'tax_rate_id' => $item->tax_rate_id,
                ]);
            }

            $order->update(['status' => 'invoiced']);

            // The item saved-hook recomputed total_amount on a freshly-queried
            // instance; refresh so the returned object matches the persisted row.
            return $invoice->refresh();
        });
    }
}
