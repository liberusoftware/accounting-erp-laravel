<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_vat_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('direction', 16)->index();
            $table->string('tax_code', 64);
            $table->decimal('net_amount', 20, 6);
            $table->decimal('tax_amount', 20, 6);
            $table->decimal('tax_rate', 10, 6)->default(0);
            $table->boolean('reverse_charge')->default(false);
            $table->string('scheme', 64)->default('standard');
            $table->unsignedInteger('box')->nullable()->index();
            $table->string('source_type', 160)->nullable();
            $table->string('source_id', 160)->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->date('occurred_on');
            $table->text('evidence')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'source_type', 'source_id']);
        });
        Schema::create('accounting_vat_returns', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('scheme', 64)->default('standard');
            $table->string('status', 32)->default('draft')->index();
            $table->json('boxes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('submission_ref', 160)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'period_start', 'period_end', 'scheme']);
        });
        Schema::create('accounting_vat_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vat_return_id')->constrained('accounting_vat_returns')->cascadeOnDelete();
            $table->unsignedInteger('box');
            $table->decimal('amount', 20, 6);
            $table->string('reason', 255);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_vat_digital_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vat_record_id')->constrained('accounting_vat_records')->cascadeOnDelete();
            $table->string('record_hash', 128)->unique();
            $table->json('payload');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_vat_digital_records');
        Schema::dropIfExists('accounting_vat_adjustments');
        Schema::dropIfExists('accounting_vat_returns');
        Schema::dropIfExists('accounting_vat_records');
    }
};
