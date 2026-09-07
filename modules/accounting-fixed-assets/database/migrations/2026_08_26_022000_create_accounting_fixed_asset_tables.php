<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_fixed_asset_categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('category_ref');
            $table->string('name');
            $table->string('asset_account_ref');
            $table->string('depreciation_account_ref');
            $table->unsignedInteger('useful_life_months');
            $table->string('depreciation_method');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'category_ref']);
        });

        Schema::create('accounting_fixed_asset_locations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('location_ref');
            $table->string('name');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'location_ref']);
        });

        Schema::create('accounting_fixed_asset_custodians', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('custodian_ref');
            $table->string('name');
            $table->string('email')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'custodian_ref']);
        });

        Schema::create('accounting_fixed_assets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('asset_ref');
            $table->string('name');
            $table->foreignId('category_id')->constrained('accounting_fixed_asset_categories')->restrictOnDelete();
            $table->string('status')->index();
            $table->date('acquired_on');
            $table->date('capitalized_on')->nullable();
            $table->decimal('cost', 18, 2);
            $table->decimal('salvage_value', 18, 2)->default(0);
            $table->decimal('net_book_value', 18, 2);
            $table->char('currency', 3);
            $table->foreignId('location_id')->nullable()->constrained('accounting_fixed_asset_locations')->nullOnDelete();
            $table->foreignId('custodian_id')->nullable()->constrained('accounting_fixed_asset_custodians')->nullOnDelete();
            $table->string('location_ref')->nullable();
            $table->string('custodian_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'asset_ref']);
        });

        Schema::create('accounting_fixed_asset_components', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('accounting_fixed_assets')->cascadeOnDelete();
            $table->string('component_ref');
            $table->string('name');
            $table->decimal('cost', 18, 2);
            $table->unsignedInteger('useful_life_months');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['asset_id', 'component_ref']);
        });

        Schema::create('accounting_fixed_asset_books', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('accounting_fixed_assets')->cascadeOnDelete();
            $table->string('book_ref');
            $table->decimal('cost', 18, 2);
            $table->decimal('accumulated_depreciation', 18, 2)->default(0);
            $table->decimal('net_book_value', 18, 2);
            $table->date('last_depreciated_on')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['asset_id', 'book_ref']);
        });

        Schema::create('accounting_fixed_asset_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('accounting_fixed_assets')->cascadeOnDelete();
            $table->string('document_ref');
            $table->string('kind');
            $table->string('file_ref');
            $table->text('description')->nullable();
            $table->string('checksum')->nullable();
            $table->string('attached_by');
            $table->timestamp('attached_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['asset_id', 'document_ref']);
        });

    }

    public function down(): void
    {
        foreach (['assets', 'documents', 'books', 'components', 'custodians', 'locations', 'categories'] as $table) {
            Schema::dropIfExists('accounting_fixed_asset_'.$table);
        }
    }
};
