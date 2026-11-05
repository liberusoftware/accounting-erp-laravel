<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_payments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('bank_connection_id')->index();
            $table->unsignedBigInteger('requested_by')->index(); // maker (for maker != checker)

            // Payment instruction (mirrors the Revolut /pay body).
            $table->string('account_id');
            $table->json('receiver');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->string('reference');
            $table->string('idempotency_key'); // reused as Revolut request_id

            // Execution state: pending_approval -> sent | failed.
            $table->string('status')->default('pending_approval')->index();
            $table->string('request_id')->nullable();
            $table->json('result')->nullable();
            $table->timestamp('sent_at')->nullable();

            // Approval state (Approvable trait): draft -> pending -> approved | rejected.
            $table->string('approval_status')->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_payments');
    }
};
