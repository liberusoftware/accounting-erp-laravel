<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_tax_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64);
            $table->string('name');
            $table->string('tax_type', 64);
            $table->string('jurisdiction_code', 32)->nullable();
            $table->decimal('rate', 12, 6)->default(0);
            $table->string('treatment', 24)->default('exclusive');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('status', 24)->default('draft');
            $table->string('exemption_code', 64)->nullable();
            $table->string('control_account_code', 64)->nullable();
            $table->string('rounding_method', 32)->default('half_up');
            $table->unsignedTinyInteger('rounding_scale')->default(2);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['code', 'jurisdiction_code', 'effective_from']);
            $table->index(['status', 'effective_from', 'effective_until']);
        });
        Schema::create('accounting_tax_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_rule_id')->constrained('accounting_tax_rules')->cascadeOnDelete();
            $table->string('source_type', 160);
            $table->string('source_id', 160);
            $table->char('snapshot_hash', 64);
            $table->json('snapshot');
            $table->unsignedBigInteger('captured_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['tax_rule_id', 'source_type', 'source_id', 'snapshot_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_tax_evidence');
        Schema::dropIfExists('accounting_tax_rules');
    }
};
