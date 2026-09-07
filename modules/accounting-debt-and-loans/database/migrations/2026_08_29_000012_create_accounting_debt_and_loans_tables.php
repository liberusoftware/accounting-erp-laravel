<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_debt_facilities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('facility_ref');
            $table->string('lender_ref');
            $table->string('currency', 3);
            $table->decimal('limit_amount', 20, 2);
            $table->decimal('drawn_amount', 20, 2)->default(0);
            $table->decimal('interest_rate', 12, 8)->default(0);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->string('status')->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'facility_ref']);
        });

        Schema::create('accounting_debt_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('facility_id')->constrained('accounting_debt_facilities')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('kind');
            $table->decimal('principal_amount', 20, 2)->default(0);
            $table->decimal('interest_amount', 20, 2)->default(0);
            $table->decimal('fee_amount', 20, 2)->default(0);
            $table->date('movement_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('scheduled')->index();
            $table->string('journal_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_debt_covenants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('facility_id')->constrained('accounting_debt_facilities')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('covenant_ref');
            $table->string('metric');
            $table->decimal('threshold', 20, 8);
            $table->string('operator')->default('lte');
            $table->decimal('last_value', 20, 8)->nullable();
            $table->string('status')->default('unmeasured');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['facility_id', 'covenant_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_debt_covenants');
        Schema::dropIfExists('accounting_debt_movements');
        Schema::dropIfExists('accounting_debt_facilities');
    }
};
