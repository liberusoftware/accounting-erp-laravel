<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_captured_documents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('source_channel');
            $t->string('file_ref');
            $t->string('checksum')->index();
            $t->string('mime_type');
            $t->string('status')->index();
            $t->string('supplier_ref')->nullable();
            $t->string('document_ref')->nullable();
            $t->json('extracted_data')->nullable();
            $t->decimal('confidence', 5, 4)->default(0);
            $t->unsignedBigInteger('duplicate_of')->nullable();
            $t->date('retention_until')->nullable();
            $t->string('reviewed_by')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_capture_events', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('document_id')->constrained('accounting_captured_documents')->cascadeOnDelete();
            $t->string('event');
            $t->string('actor_ref')->nullable();
            $t->string('adapter_ref')->nullable();
            $t->text('message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_capture_events');
        Schema::dropIfExists('accounting_captured_documents');
    }
};
