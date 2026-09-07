<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_supplier_portal_resources', function (Blueprint $t): void {
            $t->id();
            $t->string('supplier_id', 160);
            $t->string('type', 32);
            $t->string('reference', 100);
            $t->string('status', 32)->default('draft');
            $t->char('currency', 3)->default('USD');
            $t->decimal('amount', 20, 2)->default(0);
            $t->json('payload')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->text('rejected_reason')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['supplier_id', 'type', 'reference']);
            $t->index(['supplier_id', 'type', 'status']);
        });
        Schema::create('accounting_supplier_portal_documents', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('resource_id')->constrained('accounting_supplier_portal_resources')->cascadeOnDelete();
            $t->string('path');
            $t->string('original_name');
            $t->string('mime_type', 120);
            $t->char('sha256', 64);
            $t->unsignedBigInteger('uploaded_by')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['resource_id', 'sha256']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_supplier_portal_documents');
        Schema::dropIfExists('accounting_supplier_portal_resources');
    }
};
