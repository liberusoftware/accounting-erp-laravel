<?php

// src/app/Services/SubscriptionBillingService.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Subscription;

class SubscriptionBillingService
{
    private const SAFETY_CAP = 120;

    public function generateDueInvoices(Subscription $subscription): int
    {
        if ($subscription->status !== 'active' || $subscription->next_billing_date === null) {
            return 0;
        }

        $plan = $subscription->plan;
        if ($plan === null) {
            return 0;
        }

        $today = today();
        $count = 0;

        while ($subscription->next_billing_date->lte($today)) {
            if ($count >= self::SAFETY_CAP) {
                break;
            }

            $cycleDate = $subscription->next_billing_date->copy();

            // One draft invoice + line item + advance, atomic per cycle (crash-safe).
            DB::transaction(function () use ($subscription, $plan, $cycleDate): void {
                $invoice = Invoice::create([
                    'customer_id' => $subscription->customer_id,
                    'invoice_date' => $cycleDate,
                    'due_date' => $cycleDate->copy()->addDays(30),
                    'total_amount' => $plan->amount,
                    'payment_status' => 'pending',
                    'team_id' => $subscription->team_id,
                ]);
                $invoice->items()->create([
                    'description' => $plan->name,
                    'quantity' => 1,
                    'unit_price' => $plan->amount,
                    'amount' => $plan->amount,
                    'tax_amount' => 0,
                ]);

                $subscription->last_billed_at = $cycleDate;
                $subscription->next_billing_date = $this->nextDate($cycleDate, (string) $plan->interval);
                $subscription->save();
            });

            $count++;
        }

        return $count;
    }

    private function nextDate(Carbon $from, string $interval): Carbon
    {
        return match ($interval) {
            'daily' => $from->copy()->addDay(),
            'weekly' => $from->copy()->addWeek(),
            'yearly' => $from->copy()->addYear(),
            default => $from->copy()->addMonth(),
        };
    }
}
