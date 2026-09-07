<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_master_payment_terms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->string('code', 64);
            $table->string('name');
            $table->unsignedInteger('days')->default(0);
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['legal_entity_id', 'code']);
        });
        Schema::create('accounting_master_parties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->string('type', 16);
            $table->string('reference', 64)->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('tax_identifier', 128)->nullable();
            $table->foreignId('payment_term_id')->nullable()->constrained('accounting_master_payment_terms')->nullOnDelete();
            $table->decimal('credit_limit', 19, 4)->default(0);
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['legal_entity_id', 'type', 'status']);
            $table->index(['legal_entity_id', 'email']);
        });
        Schema::create('accounting_master_tax_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->string('code', 64);
            $table->string('name');
            $table->decimal('rate', 9, 4);
            $table->boolean('inclusive')->default(false);
            $table->boolean('recoverable')->default(true);
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['legal_entity_id', 'code']);
        });
        Schema::create('accounting_master_items_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->string('sku', 64);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('kind', 16)->default('service');
            $table->string('unit', 32)->nullable();
            $table->decimal('sales_price', 19, 4)->nullable();
            $table->decimal('purchase_price', 19, 4)->nullable();
            $table->foreignId('tax_profile_id')->nullable()->constrained('accounting_master_tax_profiles')->nullOnDelete();
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['legal_entity_id', 'sku']);
        });
        Schema::create('accounting_master_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->string('kind', 32)->default('primary');
            $table->string('line_one');
            $table->string('line_two')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->char('country_code', 2);
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_master_bank_detail_references', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->string('label');
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->string('masked_account', 32)->nullable();
            $table->string('credential_reference', 255);
            $table->boolean('is_primary')->default(false);
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['party_id', 'credential_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_master_bank_detail_references');
        Schema::dropIfExists('accounting_master_addresses');
        Schema::dropIfExists('accounting_master_items_services');
        Schema::dropIfExists('accounting_master_tax_profiles');
        Schema::dropIfExists('accounting_master_parties');
        Schema::dropIfExists('accounting_master_payment_terms');
    }
};
