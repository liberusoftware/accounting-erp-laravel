<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_product_service_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('code', 100);
            $table->string('name');
            $table->string('kind', 20);
            $table->text('purchase_description')->nullable();
            $table->text('sales_description')->nullable();
            $table->string('sales_account_ref')->nullable();
            $table->string('purchase_account_ref')->nullable();
            $table->string('tax_default_ref')->nullable();
            $table->string('unit', 32)->nullable();
            $table->decimal('purchase_price', 20, 4)->nullable();
            $table->decimal('sales_price', 20, 4)->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->string('status', 20)->default('active');
            $table->json('ecommerce_refs')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_product_service_items');
    }
};
