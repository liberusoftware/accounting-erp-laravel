<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperAttachment;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperProcedure;
use Liberu\Accounting\Workpapers\Actions\AddWorkpaperReference;
use Liberu\Accounting\Workpapers\Actions\AssignWorkpaperReviewer;
use Liberu\Accounting\Workpapers\Actions\ConcludeWorkpaper;
use Liberu\Accounting\Workpapers\Actions\CreateWorkpaper;
use Liberu\Accounting\Workpapers\Actions\RequestWorkpaperExport;
use Liberu\Accounting\Workpapers\Actions\RolloverWorkpaper;
use Liberu\Accounting\Workpapers\Models\Workpaper;

final class WorkpapersController extends Controller
{
    public function index(Request $request): mixed
    {
        return Workpaper::query()->where('team_id', $this->teamId($request))->withCount(['references', 'procedures', 'attachments'])->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateWorkpaper $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['title' => 'required|string|max:160', 'reference' => 'nullable|string|max:80', 'period_start' => 'nullable|date', 'period_end' => 'nullable|date', 'preparer_id' => 'nullable|integer', 'reviewer_id' => 'nullable|integer', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function procedure(Request $request, string $workpaper, AddWorkpaperProcedure $action): mixed
    {
        $model = $this->model($request, $workpaper);

        return response()->json($action->handle($model, $request->validate(['description' => 'required|string', 'status' => 'nullable|in:pending,passed,failed', 'performed_by' => 'nullable|integer', 'performed_at' => 'nullable|date', 'evidence' => 'nullable|string'])), 201);
    }

    public function reference(Request $request, string $workpaper, AddWorkpaperReference $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), $request->validate(['label' => 'required|string|max:160', 'target_type' => 'nullable|string|max:160', 'target_id' => 'nullable|string|max:160', 'notes' => 'nullable|string'])), 201);
    }

    public function attachment(Request $request, string $workpaper, AddWorkpaperAttachment $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), $request->validate(['name' => 'required|string|max:255', 'disk' => 'nullable|string|max:64', 'path' => 'required|string|max:500', 'mime_type' => 'nullable|string|max:160', 'size' => 'nullable|integer|min:0'])), 201);
    }

    public function reviewer(Request $request, string $workpaper, AssignWorkpaperReviewer $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), (int) $request->validate(['reviewer_id' => 'required|integer'])['reviewer_id']));
    }

    public function conclude(Request $request, string $workpaper, ConcludeWorkpaper $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), (string) $request->validate(['conclusion' => 'required|string'])['conclusion']));
    }

    public function rollover(Request $request, string $workpaper, RolloverWorkpaper $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), $request->validate(['period_start' => 'nullable|date', 'period_end' => 'nullable|date', 'title' => 'nullable|string|max:160'])), 201);
    }

    public function export(Request $request, string $workpaper, RequestWorkpaperExport $action): mixed
    {
        return response()->json($action->handle($this->model($request, $workpaper), (string) $request->validate(['format' => 'required|in:csv,json,pdf'])['format']), 201);
    }

    private function model(Request $request, string $workpaper): Workpaper
    {
        return Workpaper::query()->where('team_id', $this->teamId($request))->findOrFail($workpaper);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
