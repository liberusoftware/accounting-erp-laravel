<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_project_customers', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('customer_ref', 190);
            $t->string('name');
            $t->string('status', 24)->default('active');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'customer_ref']);
        });
        Schema::create('accounting_projects_and_jobs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->foreignId('customer_id')->nullable()->constrained('accounting_project_customers')->nullOnDelete();
            $t->foreignId('parent_id')->nullable()->constrained('accounting_projects_and_jobs')->nullOnDelete();
            $t->string('name');
            $t->string('code')->nullable();
            $t->text('description')->nullable();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->string('status', 24)->default('draft');
            $t->string('manager_ref', 190)->nullable();
            $t->decimal('budget_amount', 20, 2)->nullable();
            $t->char('budget_currency', 3)->nullable();
            $t->json('dimensions')->nullable();
            $t->json('source_links')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
            $t->index('parent_id');
            $t->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_projects_and_jobs');
        Schema::dropIfExists('accounting_project_customers');
    }
};
