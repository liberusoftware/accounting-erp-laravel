<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ThreeWayMatching\Actions\ApproveMatch;
use Liberu\Accounting\ThreeWayMatching\Actions\CaptureMatchEvidence;
use Liberu\Accounting\ThreeWayMatching\Actions\EvaluateMatch;
use Liberu\Accounting\ThreeWayMatching\Actions\RejectMatch;
use Liberu\Accounting\ThreeWayMatching\Actions\ResolveMatchException;
use Liberu\Accounting\ThreeWayMatching\Models\MatchException;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;
use Liberu\Accounting\ThreeWayMatching\Queries\ExceptionQuery;
use Liberu\Accounting\ThreeWayMatching\Queries\MatchQuery;
use Liberu\Accounting\ThreeWayMatchingApi\Http\Resources\MatchResource;

final class MatchController extends Controller
{
    public function index(Request $request, MatchQuery $query): mixed
    {
        return MatchResource::collection($query->paginate($request->string('status')->value() ?: null, $request->integer('page.size', 25)));
    }

    public function show(MatchRecord $match): MatchResource
    {
        return new MatchResource($match->load('exceptions', 'evidence'));
    }

    public function evaluate(Request $request, EvaluateMatch $action): MatchResource
    {
        return new MatchResource($action->handle($request->validate($this->rules())));
    }

    public function approve(Request $request, MatchRecord $match, ApproveMatch $action): MatchResource
    {
        $data = $request->validate(['override_reason' => ['nullable', 'string', 'max:2000']]);

        return new MatchResource($action->handle($match, (int) auth()->id(), $data['override_reason'] ?? null));
    }

    public function reject(Request $request, MatchRecord $match, RejectMatch $action): MatchResource
    {
        return new MatchResource($action->handle($match, $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason']));
    }

    public function resolveException(Request $request, MatchException $exception, ResolveMatchException $action): MatchResource
    {
        $data = $request->validate(['resolution' => ['required', 'string', 'max:2000'], 'waive' => ['boolean']]);
        $action->handle($exception, (int) auth()->id(), $data['resolution'], (bool) ($data['waive'] ?? false));

        return new MatchResource($exception->refresh()->load('match')->match->load('exceptions', 'evidence'));
    }

    public function exceptions(Request $request, ExceptionQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->open($request->integer('match_id') ?: null)]);
    }

    public function evidence(Request $request, MatchRecord $match, CaptureMatchEvidence $action): MatchResource
    {
        $data = $request->validate(['source_type' => ['required', 'string', 'max:160'], 'source_id' => ['required', 'string', 'max:160'], 'snapshot' => ['required', 'array']]);
        $action->handle($match, $data['source_type'], $data['source_id'], $data['snapshot'], auth()->id());

        return new MatchResource($match->refresh()->load('evidence', 'exceptions'));
    }

    private function rules(): array
    {
        return ['purchase_order_type' => ['required', 'string', 'max:160'], 'purchase_order_id' => ['required', 'string', 'max:160'], 'receipt_type' => ['required', 'string', 'max:160'], 'receipt_id' => ['required', 'string', 'max:160'], 'bill_type' => ['required', 'string', 'max:160'], 'bill_id' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'ordered_quantity' => ['required', 'numeric', 'gt:0'], 'received_quantity' => ['required', 'numeric', 'gt:0'], 'billed_quantity' => ['required', 'numeric', 'gt:0'], 'ordered_unit_price' => ['required', 'numeric', 'gte:0'], 'billed_unit_price' => ['required', 'numeric', 'gte:0'], 'expected_tax' => ['required', 'numeric', 'gte:0'], 'billed_tax' => ['required', 'numeric', 'gte:0'], 'quantity_tolerance' => ['nullable', 'numeric', 'gte:0'], 'price_tolerance' => ['nullable', 'numeric', 'gte:0'], 'tax_tolerance' => ['nullable', 'numeric', 'gte:0'], 'metadata' => ['nullable', 'array']];
    }
}
