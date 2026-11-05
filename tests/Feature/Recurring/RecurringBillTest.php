<?php

declare(strict_types=1);

namespace Tests\Feature\Recurring;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\BillItem;
use Tests\TestCase;

class RecurringBillTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        // Freeze on a mid-month day so overflow-addMonth stepping stays
        // deterministic (no month-end/February drift across runs).
        Carbon::setTestNow('2026-06-15');
    }

    #[\Override]
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function template(array $overrides = []): Bill
    {
        $template = Bill::factory()->create(array_merge([
            'is_recurring' => true,
            'recurrence_frequency' => 'monthly',
            // +1 day so the 4th occurrence (start + 3 months) lands strictly
            // after today: exactly 3 occurrences are due (the trait's cursor
            // check is inclusive of today, so a start exactly 3 months ago
            // would generate a 4th bill dated today).
            'recurrence_start' => today()->subMonthsNoOverflow(3)->addDay(),
            'last_generated' => null,
        ], $overrides));

        foreach (['Consulting retainer', 'Hosting'] as $description) {
            BillItem::create([
                'bill_id' => $template->bill_id,
                'description' => $description,
                'quantity' => 1,
                'unit_price' => 50,
                'amount' => 50,
                'tax_amount' => 0,
            ]);
        }

        return $template;
    }

    public function test_catch_up_generates_a_bill_per_missed_month_and_clones_items(): void
    {
        $template = $this->template();

        // Occurrence dates the trait walks: start, +1mo, +2mo (sequential addMonth).
        $start = $template->recurrence_start->copy();
        $occurrences = [$start->copy(), $start->copy()->addMonth(), $start->copy()->addMonth()->addMonth()];

        $created = $template->generateDue();

        $this->assertSame(3, $created);

        $children = Bill::query()
            ->where('bill_id', '!=', $template->bill_id)
            ->get();

        $this->assertCount(3, $children);

        $numbers = [];
        foreach ($children as $child) {
            $this->assertFalse($child->is_recurring);
            $this->assertSame('unpaid', $child->payment_status);
            $this->assertNotSame($template->bill_number, $child->bill_number);
            $numbers[] = $child->bill_number;

            $this->assertSame(2, BillItem::where('bill_id', $child->bill_id)->count());
        }

        // Every generated bill number is unique.
        $this->assertCount(3, array_unique($numbers));

        // Template advanced to the last generated occurrence; re-run is a no-op.
        $this->assertSame($occurrences[2]->toDateString(), $template->fresh()->last_generated->toDateString());
        $this->assertSame(0, $template->fresh()->generateDue());
    }

    public function test_recurrence_end_stops_the_catch_up(): void
    {
        $template = $this->template();

        // End falls between occurrence 2 and occurrence 3.
        $secondOccurrence = $template->recurrence_start->copy()->addMonth();
        $template->update(['recurrence_end' => $secondOccurrence->copy()->addDays(10)]);

        $this->assertSame(2, $template->generateDue());
    }
}
