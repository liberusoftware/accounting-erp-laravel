<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Vat\Actions\AddVatAdjustment;
use Liberu\Accounting\Vat\Actions\CreateVatReturn;
use Liberu\Accounting\Vat\Actions\RecordVatDigitalEvidence;
use Liberu\Accounting\Vat\Actions\RecordVatTransaction;
use Liberu\Accounting\Vat\Actions\SubmitVatReturn;
use Liberu\Accounting\Vat\Models\VatRecord;
use Liberu\Accounting\Vat\Models\VatReturn;
use Liberu\Accounting\Vat\Queries\VatReturnBoxes;

final class VatController extends Controller
{
    public function records(Request $request): mixed
    {
        return VatRecord::query()->where('team_id', $this->teamId($request))->latest('occurred_on')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function record(Request $request, RecordVatTransaction $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['direction' => 'required|in:input,output', 'tax_code' => 'required|string|max:64', 'net_amount' => 'required|numeric|min:0', 'tax_amount' => 'required|numeric|min:0', 'tax_rate' => 'nullable|numeric|min:0', 'reverse_charge' => 'nullable|boolean', 'scheme' => 'nullable|string|max:64', 'box' => 'nullable|integer|min:1', 'source_type' => 'nullable|string|max:160', 'source_id' => 'nullable|string|max:160', 'occurred_on' => 'required|date', 'evidence' => 'nullable|string', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function digitalEvidence(Request $request, string $record, RecordVatDigitalEvidence $action): mixed
    {
        return response()->json($action->handle($this->recordModel($request, $record), $request->validate(['payload' => 'required|array'])['payload']), 201);
    }

    public function returns(Request $request, VatReturnBoxes $boxes): mixed
    {
        $returns = VatReturn::query()->where('team_id', $this->teamId($request))->latest('period_end')->paginate(min(max($request->integer('per_page', 25), 1), 100));
        $returns->getCollection()->each(fn (VatReturn $vatReturn): bool => $vatReturn->setAttribute('calculated_boxes', $boxes->handle($vatReturn)) || true);

        return $returns;
    }

    public function createReturn(Request $request, CreateVatReturn $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['period_start' => 'required|date', 'period_end' => 'required|date', 'scheme' => 'nullable|string|max:64', 'boxes' => 'nullable|array', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function adjustment(Request $request, string $vatReturn, AddVatAdjustment $action): mixed
    {
        return response()->json($action->handle($this->returnModel($request, $vatReturn), $request->validate(['box' => 'required|integer|min:1', 'amount' => 'required|numeric', 'reason' => 'required|string|max:255', 'created_by' => 'nullable|integer'])), 201);
    }

    public function submit(Request $request, string $vatReturn, SubmitVatReturn $action): mixed
    {
        return response()->json($action->handle($this->returnModel($request, $vatReturn), $request->string('submission_ref')->toString() ?: null));
    }

    private function recordModel(Request $request, string $record): VatRecord
    {
        return VatRecord::query()->where('team_id', $this->teamId($request))->findOrFail($record);
    }

    private function returnModel(Request $request, string $vatReturn): VatReturn
    {
        return VatReturn::query()->where('team_id', $this->teamId($request))->findOrFail($vatReturn);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
