<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        return Invoice::where('team_id', $request->user()->current_team_id ?? -1)
            ->latest()
            ->paginate(25);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        // Scope customer/vendor references to the acting user's team so a caller
        // can't reference another tenant's OWNED records (IDOR). customers/vendors
        // have no user_id column; team_id is the boundary. NULL team_id = unowned
        // (the app currently creates them unassigned) and stays referenceable.
        // ponytail: team-or-null; tighten to strict team once records get team_id on create.
        $teamScoped = fn (string $table, string $col) => Rule::exists($table, $col)
            ->where(fn (Builder $q) => $q->where('team_id', $teamId)->orWhereNull('team_id'));

        $validated = $request->validate([
            'customer_id' => ['required', $teamScoped('customers', 'id')],
            'vendor_id' => ['sometimes', 'nullable', $teamScoped('vendors', 'vendor_id')],
            'invoice_date' => 'required|date',
            'due_date' => 'sometimes|nullable|date',
            'total_amount' => 'required|numeric',
            'notes' => 'sometimes|nullable|string',
        ]);

        $validated['team_id'] = $teamId;
        // Force the initial status server-side: a client must not be able to
        // create an invoice already marked 'paid'. It is recomputed from
        // recorded payments (see Invoice::recomputePaymentStatus).
        $validated['payment_status'] = 'pending';

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTeam($request, $invoice);

        return response()->json($invoice->load('items'));
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTeam($request, $invoice);

        $teamId = $request->user()->current_team_id;

        $validated = $request->validate([
            'customer_id' => ['sometimes', Rule::exists('customers', 'id')
                ->where(fn (Builder $q) => $q->where('team_id', $teamId)->orWhereNull('team_id'))],
            'invoice_date' => 'sometimes|date',
            'due_date' => 'sometimes|nullable|date',
            'total_amount' => 'sometimes|numeric',
            'payment_status' => 'sometimes|string',
            'notes' => 'sometimes|nullable|string',
        ]);

        $invoice->update($validated);

        return response()->json($invoice);
    }

    public function destroy(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTeam($request, $invoice);

        $invoice->delete();

        return response()->json(['deleted' => true]);
    }

    private function authorizeTeam(Request $request, Invoice $invoice): void
    {
        // ?? -1 so a tenantless caller (null current_team_id) never matches a
        // null-team_id row — (int) null === (int) null would be 0 === 0 = true.
        abort_unless((int) $invoice->team_id === ($request->user()->current_team_id ?? -1), 403);
    }
}
