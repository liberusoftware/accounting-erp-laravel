<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_withholding_tax_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('code', 64);
            $table->string('name');
            $table->string('jurisdiction', 64);
            $table->decimal('rate', 8, 4);
            $table->decimal('threshold', 20, 2)->default(0);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('status', 24)->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'code', 'effective_from']);
        });
        Schema::create('accounting_withholding_tax_certificates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('rule_id')->constrained('accounting_withholding_tax_rules')->cascadeOnDelete();
            $table->string('party_type', 64);
            $table->string('party_id', 160);
            $table->string('certificate_ref', 160);
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->string('status', 24)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'certificate_ref']);
        });
        Schema::create('accounting_withholding_tax_deductions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('rule_id')->constrained('accounting_withholding_tax_rules')->cascadeOnDelete();
            $table->string('party_type', 64);
            $table->string('party_id', 160);
            $table->string('source_ref', 160);
            $table->char('currency', 3);
            $table->decimal('gross_amount', 20, 2);
            $table->decimal('withheld_amount', 20, 2);
            $table->string('status', 24)->default('calculated');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'source_ref']);
        });
        Schema::create('accounting_withholding_tax_liabilities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('deduction_id')->constrained('accounting_withholding_tax_deductions')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->date('due_on');
            $table->string('status', 24)->default('open');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_withholding_tax_remittances', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('liability_id')->constrained('accounting_withholding_tax_liabilities')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->date('remitted_on');
            $table->string('reference', 160);
            $table->string('status', 24)->default('submitted');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_withholding_tax_statements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('jurisdiction', 64);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_amount', 20, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'jurisdiction', 'period_start', 'period_end']);
        });
        Schema::create('accounting_withholding_tax_filing_adapters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('jurisdiction', 64);
            $table->string('provider', 80);
            $table->string('status', 24)->default('configured');
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'jurisdiction', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_withholding_tax_filing_adapters');
        Schema::dropIfExists('accounting_withholding_tax_statements');
        Schema::dropIfExists('accounting_withholding_tax_remittances');
        Schema::dropIfExists('accounting_withholding_tax_liabilities');
        Schema::dropIfExists('accounting_withholding_tax_deductions');
        Schema::dropIfExists('accounting_withholding_tax_certificates');
        Schema::dropIfExists('accounting_withholding_tax_rules');
    }
};
