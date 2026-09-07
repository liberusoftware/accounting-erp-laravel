<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\EstimatesAndQuotes\Actions\AddEstimateItem;
use Liberu\Accounting\EstimatesAndQuotes\Actions\ConvertEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\CreateEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\DecideEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\SendEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Events\EstimateLifecycleChanged;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Tests\TestCase;

final class AccountingEstimatesAndQuotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_estimate_lifecycle_records_items_versions_and_history(): void
    {
        Event::fake();
        $estimate = app(CreateEstimate::class)->handle(['legal_entity_id' => 1, 'customer_ref' => 'CUS-1', 'quote_ref' => 'Q-001', 'name' => 'Implementation', 'currency' => 'gbp', 'issue_date' => '2026-08-25', 'expires_on' => '2026-09-25']);
        app(AddEstimateItem::class)->handle($estimate, ['description' => 'Consulting', 'quantity' => 2, 'unit_price' => 125]);
        $estimate = app(SendEstimate::class)->handle($estimate, 'sales-1');
        $estimate = app(DecideEstimate::class)->handle($estimate, true, null, 'customer-1');
        $estimate = app(ConvertEstimate::class)->handle($estimate, 'INV-001', 'sales-1');

        $this->assertSame(EstimateStatus::Converted, $estimate->status);
        $this->assertSame('INV-001', $estimate->converted_ref);
        $this->assertSame(250.0, (float) $estimate->items()->sum('amount'));
        $this->assertSame(['created', 'sent', 'accepted', 'converted'], $estimate->history()->orderBy('id')->pluck('event')->all());
        Event::assertDispatchedTimes(EstimateLifecycleChanged::class, 3);
    }

    public function test_estimates_cannot_be_sent_without_items_or_declined_without_a_reason(): void
    {
        $estimate = app(CreateEstimate::class)->handle(['legal_entity_id' => 1, 'customer_ref' => 'CUS-2', 'quote_ref' => 'Q-002', 'name' => 'Empty', 'currency' => 'USD', 'issue_date' => '2026-08-25']);
        $this->expectException(InvalidEstimate::class);
        app(SendEstimate::class)->handle($estimate);
    }

    public function test_api_enforces_estimate_write_ability_and_exposes_created_estimates(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['accounting.estimates-and-quotes.read']);
        $this->postJson('/api/v1/accounting/estimates-and-quotes', [])->assertForbidden();

        Sanctum::actingAs(User::factory()->create(), ['accounting.estimates-and-quotes.write']);
        $response = $this->postJson('/api/v1/accounting/estimates-and-quotes', ['legal_entity_id' => 2, 'customer_ref' => 'CUS-3', 'quote_ref' => 'Q-003', 'name' => 'API quote', 'currency' => 'USD', 'issue_date' => '2026-08-25'])->assertCreated();
        $this->assertSame('Q-003', $response->json('data.quote_ref'));
        $this->assertDatabaseHas('accounting_sales_estimates', ['quote_ref' => 'Q-003']);
    }
}
