<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_asset_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('asset_id')->index();
            $table->string('event_type', 60)->index();
            $table->date('event_date')->index();
            $table->text('description')->nullable();
            $table->string('source_type', 120)->nullable();
            $table->string('source_id', 190)->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('evidence')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['team_id', 'asset_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_asset_events');
    }
};
