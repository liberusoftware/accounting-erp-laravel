<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_cash_collection_assistants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('invoice_ref', 160);
            $table->string('customer_ref', 160)->nullable();
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->string('risk_level', 30)->default('normal');
            $table->string('reminder_status', 30)->default('not_scheduled');
            $table->timestamp('reminder_at')->nullable();
            $table->decimal('promised_amount', 20, 8)->nullable();
            $table->date('promised_date')->nullable();
            $table->string('promise_status', 30)->default('none');
            $table->string('policy_ref', 160)->nullable();
            $table->string('approval_status', 30)->default('not_required');
            $table->string('outcome', 40)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'risk_level']);
            $table->unique(['team_id', 'invoice_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_cash_collection_assistants');
    }
};
