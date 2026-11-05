<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class BillController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        return Bill::where('team_id', $request->user()->current_team_id ?? -1)
            ->latest()
            ->paginate(25);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        // Scope vendor reference to the acting user's team (vendors have no
        // user_id column) so a caller can't reference another tenant's OWNED vendor.
        // NULL team_id = unowned (app creates them unassigned) and stays referenceable.
        // ponytail: team-or-null; tighten to strict team once records get team_id on create.
        $validated = $request->validate([
            'vendor_id' => ['required', Rule::exists('vendors', 'vendor_id')
                ->where(fn (Builder $q) => $q->where('team_id', $teamId)->orWhereNull('team_id'))],
            'bill_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal_amount' => 'sometimes|numeric',
            'tax_amount' => 'sometimes|numeric',
            'total_amount' => 'required|numeric',
            'reference_number' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
        ]);

        $validated['team_id'] = $teamId;

        $bill = Bill::create($validated);

        return response()->json($bill, 201);
    }

    public function show(Request $request, Bill $bill): JsonResponse
    {
        $this->authorizeTeam($request, $bill);

        return response()->json($bill->load('items'));
    }

    public function update(Request $request, Bill $bill): JsonResponse
    {
        $this->authorizeTeam($request, $bill);

        $teamId = $request->user()->current_team_id;

        $validated = $request->validate([
            'vendor_id' => ['sometimes', Rule::exists('vendors', 'vendor_id')
                ->where(fn (Builder $q) => $q->where('team_id', $teamId)->orWhereNull('team_id'))],
            'bill_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'total_amount' => 'sometimes|numeric',
            'status' => 'sometimes|string',
            'payment_status' => 'sometimes|string',
            'notes' => 'sometimes|nullable|string',
        ]);

        $bill->update($validated);

        return response()->json($bill);
    }

    public function destroy(Request $request, Bill $bill): JsonResponse
    {
        $this->authorizeTeam($request, $bill);

        $bill->delete();

        return response()->json(['deleted' => true]);
    }

    private function authorizeTeam(Request $request, Bill $bill): void
    {
        // ?? -1 so a tenantless caller never matches a null-team_id row.
        abort_unless((int) $bill->team_id === ($request->user()->current_team_id ?? -1), 403);
    }
}
