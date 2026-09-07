<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_copilot_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->index();
            $table->string('kind')->index();
            $table->text('prompt');
            $table->json('result')->nullable();
            $table->string('status')->default('awaiting_confirmation')->index();
            $table->string('confirmation_key')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'confirmation_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_copilot_requests');
    }
};
