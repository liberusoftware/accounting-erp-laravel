<?php

declare(strict_types=1);

namespace Tests\Feature\PaymentPosting;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Payments\Pages\ListPayments;
use Livewire\Livewire;
use Tests\TestCase;

class PostPaymentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_action_posts_the_payment_to_the_ledger(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->teams()->attach($team);
        $user->forceFill(['current_team_id' => $team->id])->save();
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 250]);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 250, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);

        $this->actingAs($user);
        Filament::setTenant($team);

        Livewire::test(ListPayments::class)
            ->callTableAction('postToLedger', $payment);

        $this->assertNotNull($payment->fresh()->journal_entry_id);
    }
}
