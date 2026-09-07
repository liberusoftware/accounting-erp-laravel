<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_anomalies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('kind', 80)->index();
            $table->string('source_type', 120)->nullable();
            $table->string('source_id', 190)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('confidence', 8, 4)->nullable();
            $table->string('status', 30)->default('open')->index();
            $table->json('evidence')->nullable();
            $table->timestamp('detected_at')->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_anomalies');
    }
};
