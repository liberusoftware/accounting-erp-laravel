<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_chart_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounting_chart_accounts')->restrictOnDelete();
            $table->string('code', 64);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('type', 32);
            $table->string('normal_balance', 16);
            $table->boolean('is_control_account')->default(false);
            $table->boolean('allow_manual_entry')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('locale', 16)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['legal_entity_id', 'code']);
            $table->index(['legal_entity_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_chart_accounts');
    }
};
