<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_inventory_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('item_ref');
            $t->string('description');
            $t->string('warehouse_ref')->index();
            $t->char('currency', 3);
            $t->string('valuation_method');
            $t->string('status')->index();
            $t->decimal('quantity_on_hand', 18, 4)->default(0);
            $t->decimal('inventory_value', 18, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'item_ref', 'warehouse_ref']);
        });
        Schema::create('accounting_inventory_cost_layers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('accounting_inventory_items')->cascadeOnDelete();
            $t->string('layer_ref');
            $t->timestamp('received_at');
            $t->decimal('quantity_received', 18, 4);
            $t->decimal('quantity_remaining', 18, 4);
            $t->decimal('unit_cost', 18, 4);
            $t->decimal('total_cost', 18, 2);
            $t->string('source_ref');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['item_id', 'received_at']);
        });
        Schema::create('accounting_inventory_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('accounting_inventory_items')->cascadeOnDelete();
            $t->string('movement_ref');
            $t->string('movement_type');
            $t->decimal('quantity', 18, 4);
            $t->decimal('unit_cost', 18, 4);
            $t->decimal('total_cost', 18, 2);
            $t->string('source_ref');
            $t->timestamp('occurred_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['item_id', 'movement_ref']);
        });
        foreach (['adjustments', 'landed_costs', 'write_downs'] as $suffix) {
            Schema::create('accounting_inventory_'.$suffix, function (Blueprint $t) use ($suffix) {
                $t->id();
                $t->foreignId('item_id')->constrained('accounting_inventory_items')->cascadeOnDelete();
                $t->string($suffix === 'adjustments' ? 'adjustment_ref' : ($suffix === 'landed_costs' ? 'cost_ref' : 'write_down_ref'));
                $t->decimal($suffix === 'adjustments' ? 'quantity_delta' : 'amount', 18, 2);
                if ($suffix === 'adjustments') {
                    $t->decimal('value_delta', 18, 2);
                } $t->string('reason')->nullable();
                $t->string('actor_ref')->nullable();
                if ($suffix === 'landed_costs') {
                    $t->string('allocation_basis');
                    $t->string('source_ref');
                }$t->timestamp($suffix === 'adjustments' ? 'adjusted_at' : ($suffix === 'landed_costs' ? 'allocated_at' : 'written_down_at'));
                $t->json('metadata')->nullable();
                $t->timestamps();
            });
        }Schema::create('accounting_inventory_reconciliations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('reconciliation_ref');
            $t->string('period_ref');
            $t->decimal('subledger_value', 18, 2);
            $t->decimal('general_ledger_value', 18, 2);
            $t->decimal('variance', 18, 2);
            $t->string('status');
            $t->string('actor_ref');
            $t->timestamp('reconciled_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'reconciliation_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_inventory_reconciliations');
        foreach (['write_downs', 'landed_costs', 'adjustments'] as $suffix) {
            Schema::dropIfExists('accounting_inventory_'.$suffix);
        }Schema::dropIfExists('accounting_inventory_movements');
        Schema::dropIfExists('accounting_inventory_cost_layers');
        Schema::dropIfExists('accounting_inventory_items');
    }
};
