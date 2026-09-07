<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_dimensions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64);
            $table->string('name', 160);
            $table->string('kind', 32);
            $table->string('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['kind', 'code']);
            $table->index(['kind', 'is_active']);
        });
        Schema::create('accounting_dimension_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dimension_id')->constrained('accounting_dimensions')->cascadeOnDelete();
            $table->string('code', 64);
            $table->string('name', 160);
            $table->foreignId('parent_id')->nullable()->constrained('accounting_dimension_values')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['dimension_id', 'code']);
            $table->index(['dimension_id', 'is_active']);
        });
        Schema::create('accounting_dimension_allocations', function (Blueprint $table): void {
            $table->id();
            $table->string('allocation_key', 128);
            $table->decimal('amount', 20, 2);
            $table->string('currency', 3)->nullable();
            $table->decimal('percentage', 8, 4);
            $table->json('dimensions');
            $table->string('created_by', 191)->nullable();
            $table->timestamps();
            $table->index(['allocation_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_dimension_allocations');
        Schema::dropIfExists('accounting_dimension_values');
        Schema::dropIfExists('accounting_dimensions');
    }
};
