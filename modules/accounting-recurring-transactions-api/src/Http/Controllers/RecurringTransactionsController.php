<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\RecurringTransactions\Actions\ApproveRecurringTemplate;
use Liberu\Accounting\RecurringTransactions\Actions\CreateRecurringTemplate;
use Liberu\Accounting\RecurringTransactions\Actions\GenerateRecurringOccurrences;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;

final class RecurringTransactionsController extends Controller
{
    public function index(Request $request): mixed
    {
        return RecurringTemplate::query()->where('team_id', $this->teamId($request))->with('occurrences')->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateRecurringTemplate $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['name' => 'required|string|max:255', 'transaction_type' => 'required|string|max:80', 'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly', 'starts_on' => 'required|date', 'next_run_on' => 'nullable|date', 'ends_on' => 'nullable|date', 'automatic' => 'boolean', 'date_rules' => 'nullable|array', 'amount_rules' => 'nullable|array', 'payload' => 'required|array', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function show(Request $request, RecurringTemplate $template): RecurringTemplate
    {
        $this->assertTeam($request, $template);

        return $template->load('occurrences');
    }

    public function approve(Request $request, RecurringTemplate $template, ApproveRecurringTemplate $action): RecurringTemplate
    {
        $this->assertTeam($request, $template);

        return $action->handle($template, $request->user()?->getAuthIdentifier());
    }

    public function generate(Request $request, RecurringTemplate $template, GenerateRecurringOccurrences $action): int
    {
        $this->assertTeam($request, $template);

        return $action->handle($template, (string) $request->validate(['through_date' => 'required|date'])['through_date']);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, RecurringTemplate $template): void
    {
        abort_unless((int) $template->team_id === $this->teamId($request), 404);
    }
}
