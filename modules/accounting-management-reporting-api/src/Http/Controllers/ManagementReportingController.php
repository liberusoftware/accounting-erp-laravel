<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ManagementReporting\Actions\ArchiveReport;
use Liberu\Accounting\ManagementReporting\Actions\CreateReportPack;
use Liberu\Accounting\ManagementReporting\Actions\DeliverReport;
use Liberu\Accounting\ManagementReporting\Actions\ReviewReport;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;
use Liberu\Accounting\ManagementReporting\Queries\ReportQuery;
use Liberu\Accounting\ManagementReportingApi\Http\Resources\ReportPackResource;

final class ManagementReportingController extends Controller
{
    public function index(Request $request, ReportQuery $query): mixed
    {
        return ReportPackResource::collection($query->packs($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateReportPack $action): ReportPackResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'report_ref' => 'required|string|max:190', 'name' => 'required|string|max:190', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'currency' => 'required|string|size:3', 'owner_ref' => 'nullable|string|max:190']);

        return new ReportPackResource($action->handle($data));
    }

    public function show(ReportPack $reportPack): ReportPackResource
    {
        return new ReportPackResource($reportPack->load(['narratives', 'charts', 'reviews', 'deliveries']));
    }

    public function review(Request $request, ReportPack $reportPack, ReviewReport $action): ReportPackResource
    {
        $data = $request->validate(['decision' => 'required|in:approved,rejected,requested', 'comment' => 'nullable|string']);

        return new ReportPackResource($action->handle($reportPack, (string) $request->user()->getAuthIdentifier(), $data['decision'], $data['comment'] ?? null));
    }

    public function deliver(Request $request, ReportPack $reportPack, DeliverReport $action): JsonResponse
    {
        $data = $request->validate(['format' => 'required|in:pdf,spreadsheet', 'file_ref' => 'nullable|string|max:190', 'recipients' => 'nullable|array', 'checksum' => 'nullable|string|max:128']);

        return response()->json(['data' => $action->handle($reportPack, $data['format'], $data)], 201);
    }

    public function archive(ReportPack $reportPack, ArchiveReport $action): ReportPackResource
    {
        return new ReportPackResource($action->handle($reportPack));
    }
}
