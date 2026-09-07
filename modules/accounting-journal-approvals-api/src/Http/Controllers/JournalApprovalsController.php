<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\JournalApprovals\Actions\AddEvidence;
use Liberu\Accounting\JournalApprovals\Actions\ConfigureThreshold;
use Liberu\Accounting\JournalApprovals\Actions\DecideJournal;
use Liberu\Accounting\JournalApprovals\Actions\PostJournal;
use Liberu\Accounting\JournalApprovals\Actions\SubmitJournal;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;
use Liberu\Accounting\JournalApprovals\Queries\JournalApprovalQuery;
use Liberu\Accounting\JournalApprovalsApi\Http\Resources\JournalApprovalResource;

final class JournalApprovalsController extends Controller
{
    public function __construct(private readonly JournalApprovalQuery $query) {}

    public function index(Request $request): JournalApprovalResource
    {
        $status = $request->filled('status') ? ApprovalStatus::from($request->string('status')->toString()) : null;

        return new JournalApprovalResource($this->query->paginate($request->integer('team_id') ?: null, $status, $request->integer('per_page', 25)));
    }

    public function store(Request $request, SubmitJournal $action): JournalApprovalResource
    {
        return new JournalApprovalResource($action->handle($request->except('evidence'), (array) $request->input('evidence', [])));
    }

    public function show(JournalApproval $approval): JournalApprovalResource
    {
        return new JournalApprovalResource($approval->load(['decisions', 'evidence']));
    }

    public function decide(Request $request, JournalApproval $approval, DecideJournal $action): JournalApprovalResource
    {
        $data = $request->validate(['actor_ref' => ['required', 'string', 'max:255'], 'approved' => ['required', 'boolean'], 'comment' => ['nullable', 'string', 'max:5000']]);

        return new JournalApprovalResource($action->handle($approval, $data['actor_ref'], (bool) $data['approved'], $data['comment'] ?? null));
    }

    public function post(Request $request, JournalApproval $approval, PostJournal $action): JournalApprovalResource
    {
        $data = $request->validate(['emergency' => ['sometimes', 'boolean'], 'reason' => ['nullable', 'string', 'max:5000']]);

        return new JournalApprovalResource($action->handle($approval, (bool) ($data['emergency'] ?? false), $data['reason'] ?? null));
    }

    public function evidence(Request $request, JournalApproval $approval, AddEvidence $action): JournalApprovalResource
    {
        $data = $request->validate(['kind' => ['required', 'string', 'max:100'], 'file_ref' => ['nullable', 'string', 'max:500'], 'description' => ['nullable', 'string', 'max:5000'], 'checksum' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']]);
        $action->handle($approval, $data);

        return new JournalApprovalResource($approval->load(['decisions', 'evidence']));
    }

    public function threshold(Request $request, ConfigureThreshold $action): JsonResponse
    {
        $data = $request->validate([
            'team_id' => ['nullable', 'integer'], 'journal_type' => ['required', 'string', 'max:100'],
            'minimum_amount' => ['required', 'numeric', 'min:0'], 'reviewer_role' => ['required', 'string', 'max:100'],
            'required_approvals' => ['sometimes', 'integer', 'min:1'], 'emergency_allowed' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'], 'metadata' => ['nullable', 'array'],
        ]);

        return response()->json(['data' => $action->handle($data)], 201);
    }
}
