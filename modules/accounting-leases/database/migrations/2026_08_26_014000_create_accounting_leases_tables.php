<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_leases', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('lease_ref', 190);
            $t->string('name', 190);
            $t->string('lessor_ref', 190);
            $t->string('asset_ref', 190)->nullable();
            $t->date('commencement_date');
            $t->date('end_date');
            $t->char('currency', 3);
            $t->decimal('payment_amount', 20, 2);
            $t->string('payment_frequency', 20)->default('monthly');
            $t->decimal('interest_rate', 12, 8);
            $t->decimal('discount_rate', 12, 8);
            $t->unsignedInteger('useful_life_months');
            $t->string('status', 24)->default('draft');
            $t->decimal('right_of_use_asset', 20, 2)->default(0);
            $t->decimal('lease_liability', 20, 2)->default(0);
            $t->decimal('accumulated_depreciation', 20, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'lease_ref']);
            $t->index(['team_id', 'status', 'end_date']);
        });
        Schema::create('accounting_lease_payments', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('lease_id')->constrained('accounting_leases')->cascadeOnDelete();
            $t->string('payment_ref', 190);
            $t->date('due_date');
            $t->decimal('amount', 20, 2);
            $t->decimal('principal_amount', 20, 2)->default(0);
            $t->decimal('interest_amount', 20, 2)->default(0);
            $t->decimal('depreciation_amount', 20, 2)->default(0);
            $t->string('status', 24)->default('scheduled');
            $t->timestamp('posted_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['lease_id', 'payment_ref']);
            $t->index(['lease_id', 'due_date', 'status']);
        });
        Schema::create('accounting_lease_modifications', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('lease_id')->constrained('accounting_leases')->cascadeOnDelete();
            $t->string('modification_ref', 190);
            $t->date('effective_date');
            $t->string('kind', 50);
            $t->date('old_term_end')->nullable();
            $t->date('new_term_end')->nullable();
            $t->decimal('old_payment_amount', 20, 2)->nullable();
            $t->decimal('new_payment_amount', 20, 2)->nullable();
            $t->decimal('adjustment_amount', 20, 2)->nullable();
            $t->text('reason')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['lease_id', 'modification_ref']);
        });
        Schema::create('accounting_lease_disclosures', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('lease_id')->constrained('accounting_leases')->cascadeOnDelete();
            $t->date('as_of_date');
            $t->decimal('remaining_liability', 20, 2);
            $t->decimal('current_liability', 20, 2);
            $t->decimal('non_current_liability', 20, 2);
            $t->json('future_payments');
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['lease_id', 'as_of_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_lease_disclosures');
        Schema::dropIfExists('accounting_lease_modifications');
        Schema::dropIfExists('accounting_lease_payments');
        Schema::dropIfExists('accounting_leases');
    }
};
