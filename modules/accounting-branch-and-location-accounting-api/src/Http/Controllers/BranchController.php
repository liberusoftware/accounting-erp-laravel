<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\BranchLocationAccounting\Actions\AllocateBranchValue;
use Liberu\Accounting\BranchLocationAccounting\Actions\CreateBranch;
use Liberu\Accounting\BranchLocationAccounting\Models\Branch;
use Liberu\Accounting\BranchLocationAccounting\Queries\BranchQuery;

final class BranchController extends Controller
{
    public function index(Request $request, BranchQuery $query): mixed
    {
        return response()->json(['data' => $query->forTeam($this->teamId($request), $request->string('status')->toString() ?: null)]);
    }

    public function store(Request $request, CreateBranch $action): mixed
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:80'], 'name' => ['required', 'string', 'max:255'], 'location' => ['nullable', 'string', 'max:160'], 'local_tax_code' => ['nullable', 'string', 'max:80'], 'sequence_prefix' => ['nullable', 'string', 'max:80'], 'allocation_rule' => ['nullable', 'string', 'max:160'], 'performance_target' => ['nullable', 'numeric', 'gte:0'], 'statutory_reference' => ['nullable', 'string', 'max:160']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId($request)])], 201);
    }

    public function allocate(Request $request, string $branch, AllocateBranchValue $action): mixed
    {
        $record = Branch::query()->where('team_id', $this->teamId($request))->findOrFail($branch);
        $data = $request->validate(['amount' => ['required', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle($record, (float) $data['amount'])]);
    }

    private function teamId(Request $request): int
    {
        abort_if(($id = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return (int) $id;
    }
}
