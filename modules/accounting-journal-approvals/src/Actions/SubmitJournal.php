<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Exceptions\InvalidApproval;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

final class SubmitJournal
{
    public function handle(array $attributes, array $evidence = []): JournalApproval
    {
        $ref = trim((string) ($attributes['approval_ref'] ?? ''));
        $amount = (float) ($attributes['amount'] ?? 0);
        foreach (['journal_type', 'journal_source', 'journal_ref', 'preparer_ref', 'currency'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidApproval("Missing journal field [{$key}].");
            }
        }if ($ref === '' || $amount <= 0) {
            throw new InvalidApproval('Approval reference and positive journal amount are required.');
        }

        return DB::transaction(function () use ($attributes, $evidence, $ref, $amount): JournalApproval {
            $existing = JournalApproval::query()->where(['team_id' => $attributes['team_id'] ?? null, 'approval_ref' => $ref])->first();
            if ($existing) {
                return $existing->load('evidence');
            }$approval = JournalApproval::create(['team_id' => $attributes['team_id'] ?? null, 'approval_ref' => $ref, 'journal_type' => $attributes['journal_type'], 'journal_source' => $attributes['journal_source'], 'journal_ref' => $attributes['journal_ref'], 'preparer_ref' => $attributes['preparer_ref'], 'reviewer_ref' => $attributes['reviewer_ref'] ?? null, 'currency' => strtoupper($attributes['currency']), 'amount' => $amount, 'threshold_amount' => $attributes['threshold_amount'] ?? null, 'status' => ApprovalStatus::Pending, 'submitted_at' => now(), 'metadata' => $attributes['metadata'] ?? null]);
            foreach ($evidence as $item) {
                if (! is_array($item) || blank($item['kind'] ?? null)) {
                    throw new InvalidApproval('Each evidence item requires a kind.');
                }$approval->evidence()->create(['kind' => $item['kind'], 'file_ref' => $item['file_ref'] ?? null, 'description' => $item['description'] ?? null, 'checksum' => $item['checksum'] ?? null, 'metadata' => $item['metadata'] ?? null]);
            }

            return $approval->load('evidence');
        });
    }
}
