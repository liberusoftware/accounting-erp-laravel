<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;

uses(RefreshDatabase::class);

it('requires the sales tax read ability and returns authorized records', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, []);
    $this->getJson('/api/v1/accounting/sales-tax-and-gst')->assertForbidden();

    Sanctum::actingAs($user, ['accounting.sales-tax-and-gst.read']);
    SalesTaxRecord::query()->create(['context_id' => 'entity-1', 'type' => 'liability', 'jurisdiction' => 'CA-ON', 'period_start' => '2026-01-01', 'period_end' => '2026-03-31']);
    $this->getJson('/api/v1/accounting/sales-tax-and-gst')->assertOk()->assertJsonCount(1, 'data');
});

it('requires the sales tax write ability', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.sales-tax-and-gst.read']);

    $this->postJson('/api/v1/accounting/sales-tax-and-gst', ['context_id' => 'entity-1', 'type' => 'liability', 'jurisdiction' => 'CA-ON', 'period_start' => '2026-01-01', 'period_end' => '2026-03-31'])->assertForbidden();
});
