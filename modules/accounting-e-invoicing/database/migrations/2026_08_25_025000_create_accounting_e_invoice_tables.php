<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_e_invoice_documents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('legal_entity_id')->index();
            $t->string('document_ref');
            $t->string('document_type');
            $t->string('format');
            $t->string('status')->index();
            $t->string('tax_id');
            $t->string('counterparty_ref');
            $t->char('currency', 3);
            $t->string('provider_ref')->nullable();
            $t->json('payload');
            $t->text('signature')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('received_at')->nullable();
            $t->timestamp('archived_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['legal_entity_id', 'document_ref']);
        });
        Schema::create('accounting_e_invoice_events', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('document_id')->constrained('accounting_e_invoice_documents')->cascadeOnDelete();
            $t->string('event');
            $t->string('provider_ref')->nullable();
            $t->string('actor_ref')->nullable();
            $t->text('message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_e_invoice_events');
        Schema::dropIfExists('accounting_e_invoice_documents');
    }
};
