<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_three_way_matches', function (Blueprint $table): void {
            $table->id();
            $table->string('purchase_order_type', 160);
            $table->string('purchase_order_id', 160);
            $table->string('receipt_type', 160);
            $table->string('receipt_id', 160);
            $table->string('bill_type', 160);
            $table->string('bill_id', 160);
            $table->char('currency', 3);
            $table->decimal('ordered_quantity', 20, 4);
            $table->decimal('received_quantity', 20, 4);
            $table->decimal('billed_quantity', 20, 4);
            $table->decimal('ordered_unit_price', 20, 4);
            $table->decimal('billed_unit_price', 20, 4);
            $table->decimal('expected_tax', 20, 2);
            $table->decimal('billed_tax', 20, 2);
            $table->decimal('quantity_tolerance', 20, 4)->default(0);
            $table->decimal('price_tolerance', 20, 4)->default(0);
            $table->decimal('tax_tolerance', 20, 2)->default(0);
            $table->string('status', 24)->default('exception');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['purchase_order_type', 'purchase_order_id', 'receipt_type', 'receipt_id', 'bill_type', 'bill_id']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('accounting_three_way_match_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('accounting_three_way_matches')->cascadeOnDelete();
            $table->string('kind', 48);
            $table->string('severity', 24)->default('blocking');
            $table->string('status', 24)->default('open');
            $table->decimal('expected_value', 20, 4)->nullable();
            $table->decimal('actual_value', 20, 4)->nullable();
            $table->decimal('tolerance', 20, 4)->nullable();
            $table->text('resolution')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['match_id', 'status', 'severity']);
        });

        Schema::create('accounting_three_way_match_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('accounting_three_way_matches')->cascadeOnDelete();
            $table->string('source_type', 160);
            $table->string('source_id', 160);
            $table->char('snapshot_hash', 64);
            $table->json('snapshot');
            $table->unsignedBigInteger('captured_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['match_id', 'source_type', 'source_id', 'snapshot_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_three_way_match_evidence');
        Schema::dropIfExists('accounting_three_way_match_exceptions');
        Schema::dropIfExists('accounting_three_way_matches');
    }
};
