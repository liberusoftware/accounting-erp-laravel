<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\TaxReturns\Actions\AmendTaxReturn;
use Liberu\Accounting\TaxReturns\Actions\CreateTaxReturn;
use Liberu\Accounting\TaxReturns\Actions\SubmitTaxReturn;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;

final class TaxReturnsController extends Controller
{
    public function index(Request $request): mixed
    {
        return TaxReturn::query()->where('team_id', $this->teamId($request))->with('lines')->latest('period_end')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateTaxReturn $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['tax_type' => 'required|string|max:32', 'jurisdiction' => 'required|string|max:64', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'due_on' => 'nullable|date', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function submit(Request $request, string $taxReturn, SubmitTaxReturn $action): mixed
    {
        return $action->handle($this->taxReturn($request, $taxReturn), (string) $request->validate(['external_reference' => 'required|string|max:160'])['external_reference']);
    }

    public function amend(Request $request, string $taxReturn, AmendTaxReturn $action): mixed
    {
        return $action->handle($this->taxReturn($request, $taxReturn));
    }

    private function taxReturn(Request $request, string $taxReturn): TaxReturn
    {
        return TaxReturn::query()->where('team_id', $this->teamId($request))->findOrFail($taxReturn);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
