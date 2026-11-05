<?php

// src/tests/Feature/Document/DocumentTenancyTest.php
declare(strict_types=1);

namespace Tests\Feature\Document;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\DocumentService;
use Tests\TestCase;

class DocumentTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_inherits_owner_team(): void
    {
        Storage::fake(config('documents.disk'));
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id]);

        $doc = app(DocumentService::class)->attach($invoice, UploadedFile::fake()->create('c.pdf', 10));

        $this->assertSame($team->id, (int) $doc->team_id);
    }
}
