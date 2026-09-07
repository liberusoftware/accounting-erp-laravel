<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_audit_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('reference')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamp('due_at')->nullable()->index();
            $table->json('evidence')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_audit_requests');
    }
};
