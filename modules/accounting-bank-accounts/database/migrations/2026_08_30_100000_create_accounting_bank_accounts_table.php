<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_bank_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained('accounting_legal_entities')->cascadeOnDelete();
            $table->string('owner_type', 160)->nullable();
            $table->string('owner_id', 160)->nullable();
            $table->string('name', 160);
            $table->string('institution_name', 160)->nullable();
            $table->string('account_type', 24);
            $table->char('currency', 3);
            $table->decimal('opening_balance', 20, 2)->default(0);
            $table->date('opening_date');
            $table->decimal('current_balance', 20, 2)->default(0);
            $table->text('account_number')->nullable();
            $table->text('routing_number')->nullable();
            $table->string('feed_reference', 160)->nullable();
            $table->string('status', 16)->default('active');
            $table->timestamp('closed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['legal_entity_id', 'name']);
            $table->index(['legal_entity_id', 'status', 'account_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_bank_accounts');
    }
};
