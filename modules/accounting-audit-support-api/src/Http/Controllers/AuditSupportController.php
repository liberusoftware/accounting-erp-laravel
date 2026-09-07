<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AuditSupport\Actions\CreateAuditRequest;
use Liberu\Accounting\AuditSupport\Actions\SubmitAuditRequest;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;
use Liberu\Accounting\AuditSupport\Queries\AuditRequestQuery;
use Liberu\Accounting\AuditSupportApi\Http\Resources\AuditRequestResource;

final class AuditSupportController extends Controller
{
    public function index(Request $request, AuditRequestQuery $query): mixed
    {
        Gate::authorize('viewAny', AuditRequest::class);

        return AuditRequestResource::collection($query->paginate($this->team($request), $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateAuditRequest $action): AuditRequestResource
    {
        Gate::authorize('create', AuditRequest::class);
        $data = $request->validate(['reference' => 'nullable|string|max:80', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'owner_id' => 'nullable|integer', 'due_at' => 'nullable|date', 'evidence' => 'nullable|array']);

        return new AuditRequestResource($action->handle([...$data, 'team_id' => $this->team($request)]));
    }

    public function submit(Request $request, AuditRequest $auditRequest, SubmitAuditRequest $action): AuditRequestResource
    {
        $auditRequest = AuditRequest::findOrFail((int) $request->route('auditRequest'));
        Gate::authorize('update', $auditRequest);

        return new AuditRequestResource($action->handle($auditRequest));
    }

    private function team(Request $request): int
    {
        abort_if(($id = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return (int) $id;
    }
}
