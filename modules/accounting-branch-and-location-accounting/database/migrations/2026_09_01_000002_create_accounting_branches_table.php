<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_branches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('code');
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('local_tax_code')->nullable();
            $table->string('sequence_prefix')->nullable();
            $table->string('allocation_rule')->nullable();
            $table->decimal('performance_target', 20, 8)->nullable();
            $table->string('statutory_reference')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_branches');
    }
};
