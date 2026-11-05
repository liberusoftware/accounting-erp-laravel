<?php

declare(strict_types=1);

namespace Tests\Feature\InvoicePosting;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Invoices\Pages\ListInvoices;
use Livewire\Livewire;
use Tests\TestCase;

class PostInvoiceActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_action_posts_the_invoice_to_the_ledger(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->teams()->attach($team);
        $user->forceFill(['current_team_id' => $team->id])->save();
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 250]);

        $this->actingAs($user);
        Filament::setTenant($team);

        Livewire::test(ListInvoices::class)
            ->callTableAction('postToLedger', $invoice);

        $this->assertNotNull($invoice->fresh()->journal_entry_id);
    }
}
