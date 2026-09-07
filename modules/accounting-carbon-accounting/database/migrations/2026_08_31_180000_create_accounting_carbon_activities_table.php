<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_carbon_activities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->date('activity_date')->index();
            $table->string('scope', 20)->index();
            $table->string('category', 80)->index();
            $table->string('description');
            $table->decimal('quantity', 20, 6);
            $table->string('unit', 30);
            $table->decimal('emission_factor', 20, 8);
            $table->decimal('co2e', 20, 8);
            $table->string('factor_source')->nullable();
            $table->json('evidence')->nullable();
            $table->boolean('is_estimate')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_carbon_activities');
    }
};
