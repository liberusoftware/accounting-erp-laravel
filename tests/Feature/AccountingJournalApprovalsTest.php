<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\JournalApprovals\Actions\DecideJournal;
use Liberu\Accounting\JournalApprovals\Actions\PostJournal;
use Liberu\Accounting\JournalApprovals\Actions\SubmitJournal;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Tests\TestCase;

final class AccountingJournalApprovalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_journal_can_be_submitted_approved_and_posted_with_evidence(): void
    {
        $approval = app(SubmitJournal::class)->handle([
            'team_id' => 1, 'approval_ref' => 'APR-001', 'journal_type' => 'accrual',
            'journal_source' => 'ledger', 'journal_ref' => 'J-001', 'preparer_ref' => 'user-1',
            'currency' => 'gbp', 'amount' => 1500,
        ], [['kind' => 'invoice', 'file_ref' => 'file-1']]);

        $this->assertSame(ApprovalStatus::Pending, $approval->status);
        $approval = app(DecideJournal::class)->handle($approval, 'user-2', true);
        $approval = app(PostJournal::class)->handle($approval);

        $this->assertSame(ApprovalStatus::Posted, $approval->status);
        $this->assertCount(1, $approval->decisions);
        $this->assertCount(1, $approval->evidence);
    }

    public function test_rejections_require_a_reason_and_emergency_posts_require_a_reason(): void
    {
        $approval = app(SubmitJournal::class)->handle([
            'approval_ref' => 'APR-002', 'journal_type' => 'accrual', 'journal_source' => 'ledger',
            'journal_ref' => 'J-002', 'preparer_ref' => 'user-1', 'currency' => 'USD', 'amount' => 20,
        ]);

        $this->expectException(InvalidApproval::class);
        app(DecideJournal::class)->handle($approval, 'user-2', false);
    }
}
