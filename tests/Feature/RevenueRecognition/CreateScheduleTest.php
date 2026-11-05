<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueRecognition;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;
use Tests\TestCase;

class CreateScheduleTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0:Invoice,1:Account,2:Account} */
    private function fixtures(float $total): array
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => $total, 'invoice_date' => '2026-01-15']);
        $deferred = Account::create(['account_number' => 2400, 'account_name' => 'Deferred', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id]);
        $revenue = Account::create(['account_number' => 4000, 'account_name' => 'Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id]);

        return [$invoice, $deferred, $revenue];
    }

    public function test_generates_straight_line_entries_summing_to_total(): void
    {
        [$invoice, $deferred, $revenue] = $this->fixtures(1000.00); // 1000 / 3 = 333.33, last = 333.34
        $schedule = app(RevenueRecognitionService::class)->createFromInvoice($invoice, 3, $deferred, $revenue);

        $this->assertSame(3, $schedule->entries()->count());
        $amounts = $schedule->entries()->orderBy('period_number')->pluck('amount')->map(fn ($a) => (string) $a)->all();
        $this->assertSame(['333.33', '333.33', '333.34'], $amounts);
        // entries sum to the exact invoice total (no lost/gained cent)
        $this->assertSame('1000.00', number_format((float) $schedule->entries()->sum('amount'), 2, '.', ''));
        // recognition dates step one month from invoice_date
        $dates = $schedule->entries()->orderBy('period_number')->pluck('recognition_date')->map(fn ($d) => $d->toDateString())->all();
        $this->assertSame(['2026-01-15', '2026-02-15', '2026-03-15'], $dates);
        $this->assertSame($invoice->team_id, (int) $schedule->team_id);
    }

    public function test_rejects_zero_periods_and_duplicate_schedule(): void
    {
        [$invoice, $deferred, $revenue] = $this->fixtures(500.00);
        $service = app(RevenueRecognitionService::class);

        $service->createFromInvoice($invoice, 5, $deferred, $revenue);

        $this->expectException(InvalidArgumentException::class);
        $service->createFromInvoice($invoice->fresh(), 5, $deferred, $revenue); // second schedule for same invoice
    }

    public function test_rejects_same_deferred_and_revenue_account(): void
    {
        [$invoice, $deferred] = $this->fixtures(500.00);

        $this->expectException(InvalidArgumentException::class);
        app(RevenueRecognitionService::class)->createFromInvoice($invoice, 5, $deferred, $deferred);
    }
}
