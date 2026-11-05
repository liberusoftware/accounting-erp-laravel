<?php

declare(strict_types=1);

namespace Tests\Feature\InvoicePosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Tests\TestCase;

class PostInvoiceCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_posts_the_invoice_by_id(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 400]);

        $this->artisan('invoices:post', ['invoice' => $invoice->id])->assertSuccessful();

        $this->assertNotNull($invoice->fresh()->journal_entry_id);
        $this->assertSame(1, JournalEntry::where('team_id', $team->id)->count());
    }

    public function test_command_fails_for_unknown_invoice(): void
    {
        $this->artisan('invoices:post', ['invoice' => 999999])->assertFailed();
    }
}
