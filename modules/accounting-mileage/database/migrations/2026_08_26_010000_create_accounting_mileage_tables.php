<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_mileage_vehicles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('owner_ref', 190)->nullable();
            $t->string('registration', 40);
            $t->string('make', 80)->nullable();
            $t->string('model', 80)->nullable();
            $t->string('fuel_type', 30)->nullable();
            $t->decimal('co2_g_per_km', 10, 2)->nullable();
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'registration']);
            $t->index(['team_id', 'active']);
        });
        Schema::create('accounting_mileage_rates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('region', 80);
            $t->string('vehicle_type', 40);
            $t->char('currency', 3);
            $t->decimal('rate_per_distance', 16, 6);
            $t->date('effective_from');
            $t->date('effective_until')->nullable();
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'region', 'vehicle_type', 'effective_from']);
        });
        Schema::create('accounting_mileage_policies', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('name', 120);
            $t->string('region', 80)->nullable();
            $t->decimal('max_distance_per_trip', 16, 2)->nullable();
            $t->decimal('max_distance_per_day', 16, 2)->nullable();
            $t->boolean('requires_purpose')->default(true);
            $t->boolean('requires_project')->default(false);
            $t->decimal('approval_threshold', 16, 2)->nullable();
            $t->char('currency', 3);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'region', 'active']);
        });
        Schema::create('accounting_mileage_trips', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('trip_ref', 190);
            $t->string('employee_ref', 190);
            $t->foreignId('vehicle_id')->nullable()->constrained('accounting_mileage_vehicles')->nullOnDelete();
            $t->foreignId('rate_id')->nullable()->constrained('accounting_mileage_rates')->nullOnDelete();
            $t->foreignId('policy_id')->nullable()->constrained('accounting_mileage_policies')->nullOnDelete();
            $t->string('project_ref', 190)->nullable();
            $t->string('origin', 190)->nullable();
            $t->string('destination', 190)->nullable();
            $t->date('trip_date');
            $t->decimal('distance', 16, 2);
            $t->string('distance_unit', 12)->default('km');
            $t->text('business_purpose')->nullable();
            $t->string('region', 80);
            $t->char('currency', 3);
            $t->decimal('reimbursement_amount', 20, 2)->default(0);
            $t->string('status', 24)->default('draft');
            $t->string('source', 24)->default('manual');
            $t->string('source_hash', 64)->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamp('reimbursed_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'trip_ref']);
            $t->index(['team_id', 'employee_ref', 'trip_date', 'status']);
        });
        Schema::create('accounting_mileage_approvals', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('trip_id')->constrained('accounting_mileage_trips')->cascadeOnDelete();
            $t->string('actor_ref', 190);
            $t->string('decision', 24);
            $t->text('reason')->nullable();
            $t->timestamp('decided_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['trip_id', 'decision']);
        });
        Schema::create('accounting_mileage_reimbursements', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('trip_id')->constrained('accounting_mileage_trips')->cascadeOnDelete();
            $t->string('payee_ref', 190);
            $t->char('currency', 3);
            $t->decimal('amount', 20, 2);
            $t->string('status', 24)->default('pending');
            $t->string('external_ref', 190)->nullable();
            $t->text('failure_message')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['trip_id', 'payee_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_mileage_reimbursements');
        Schema::dropIfExists('accounting_mileage_approvals');
        Schema::dropIfExists('accounting_mileage_trips');
        Schema::dropIfExists('accounting_mileage_policies');
        Schema::dropIfExists('accounting_mileage_rates');
        Schema::dropIfExists('accounting_mileage_vehicles');
    }
};
