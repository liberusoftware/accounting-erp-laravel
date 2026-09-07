<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_purchase_requisitions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('requester_ref', 190);
            $t->string('title')->nullable();
            $t->char('currency', 3);
            $t->decimal('total_amount', 20, 2);
            $t->json('lines');
            $t->json('coding')->nullable();
            $t->json('budget')->nullable();
            $t->json('attachments')->nullable();
            $t->string('status', 24)->default('draft');
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->string('sourcing_ref', 190)->nullable();
            $t->string('converted_ref', 190)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_requisition_approvals', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('requisition_id')->constrained('accounting_purchase_requisitions')->cascadeOnDelete();
            $t->string('approver_ref', 190);
            $t->string('decision', 24);
            $t->text('reason')->nullable();
            $t->timestamp('decided_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['requisition_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_requisition_approvals');
        Schema::dropIfExists('accounting_purchase_requisitions');
    }
};
