<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\AllocateCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\ApproveCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\CreateCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Enums\CreditNoteStatus;
use Liberu\Accounting\CreditNotesAndAdjustments\Exceptions\InvalidCreditNote;

uses(RefreshDatabase::class);

it('approves and partially allocates a credit note', function (): void {
    $note = app(CreateCreditNote::class)->handle(['team_id' => 81, 'customer_id' => 'cust-1', 'credit_ref' => 'CN-1', 'reason' => 'Returned goods', 'currency' => 'GBP', 'amount' => 300, 'tax_amount' => 50, 'evidence' => ['return' => 'R-1']]);
    app(ApproveCreditNote::class)->handle($note, 9);
    app(AllocateCreditNote::class)->handle($note->refresh(), 'INV-1', 200);
    expect($note->refresh()->status)->toBe(CreditNoteStatus::PartiallyAllocated)->and((float) $note->allocated_amount)->toBe(200.0);
});

it('rejects allocation before approval', function (): void {
    $note = app(CreateCreditNote::class)->handle(['team_id' => 81, 'customer_id' => 'cust-1', 'credit_ref' => 'CN-2', 'reason' => 'Correction', 'currency' => 'GBP', 'amount' => 100]);
    expect(fn () => app(AllocateCreditNote::class)->handle($note, 'INV-2', 10))->toThrow(InvalidCreditNote::class);
});
