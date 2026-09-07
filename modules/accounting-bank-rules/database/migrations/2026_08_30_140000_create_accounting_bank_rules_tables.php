<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_bank_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name', 160);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('enabled')->default(true);
            $table->json('conditions');
            $table->json('actions');
            $table->string('automation_mode', 30)->default('suggest');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
            $table->index(['team_id', 'enabled', 'priority']);
        });
        Schema::create('accounting_bank_rule_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('rule_id')->constrained('accounting_bank_rules')->cascadeOnDelete();
            $table->string('transaction_reference', 180);
            $table->string('outcome', 40);
            $table->boolean('matched');
            $table->json('actions_applied')->nullable();
            $table->string('actor_reference', 180)->nullable();
            $table->timestamp('created_at');
            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_bank_rule_history');
        Schema::dropIfExists('accounting_bank_rules');
    }
};
