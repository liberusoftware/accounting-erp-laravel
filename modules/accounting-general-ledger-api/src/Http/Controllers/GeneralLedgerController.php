<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\GeneralLedger\Actions\CreateJournal;
use Liberu\Accounting\GeneralLedger\Actions\CreateTypedJournal;
use Liberu\Accounting\GeneralLedger\Actions\GenerateRecurringJournal;
use Liberu\Accounting\GeneralLedger\Actions\PostJournal;
use Liberu\Accounting\GeneralLedger\Actions\ReverseJournal;
use Liberu\Accounting\GeneralLedger\Actions\SaveRecurringJournal;
use Liberu\Accounting\GeneralLedger\Enums\JournalType;
use Liberu\Accounting\GeneralLedger\Exceptions\InvalidJournal;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Liberu\Accounting\GeneralLedger\Models\RecurringJournal;
use Liberu\Accounting\GeneralLedger\Queries\AccountBalances;
use Liberu\Accounting\GeneralLedgerApi\Http\Resources\JournalResource;

final class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', JournalEntry::class);

        return JournalResource::collection(JournalEntry::with('lines')->where('book_id', $request->integer('book_id'))->latest()->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function show(string $journal): JournalResource
    {
        $model = JournalEntry::with('lines')->findOrFail($journal);
        Gate::authorize('view', $model);

        return new JournalResource($model);
    }

    public function store(Request $request, CreateJournal $create)
    {
        Gate::authorize('create', JournalEntry::class);
        $data = $request->validate(['book_id' => ['required', 'integer'], 'entry_number' => ['nullable', 'string', 'max:80'], 'entry_date' => ['required', 'date'], 'journal_type' => ['nullable', 'in:general,recurring,correction,allocation,accrual,prepayment'], 'description' => ['nullable', 'string', 'max:255'], 'lines' => ['required', 'array', 'min:2'], 'lines.*.account_id' => ['required', 'integer'], 'lines.*.debit' => ['nullable', 'numeric', 'min:0'], 'lines.*.credit' => ['nullable', 'numeric', 'min:0'], 'lines.*.description' => ['nullable', 'string', 'max:255']]);
        try {
            return (new JournalResource($create->handle(collect($data)->except('lines')->all(), $data['lines'])))->response()->setStatusCode(201);
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['lines' => $e->getMessage()]);
        }
    }

    public function storeTyped(Request $request, string $type, CreateTypedJournal $create): JsonResource
    {
        Gate::authorize('create', JournalEntry::class);
        $data = $request->validate(['book_id' => ['required', 'integer'], 'entry_number' => ['nullable', 'string', 'max:80'], 'entry_date' => ['required', 'date'], 'description' => ['nullable', 'string', 'max:255'], 'source_type' => ['nullable', 'string', 'max:191'], 'source_id' => ['nullable', 'string', 'max:191'], 'lines' => ['required', 'array', 'min:2'], 'lines.*.account_id' => ['required', 'integer'], 'lines.*.debit' => ['nullable', 'numeric', 'min:0'], 'lines.*.credit' => ['nullable', 'numeric', 'min:0'], 'lines.*.description' => ['nullable', 'string', 'max:255']]);
        $map = ['corrections' => JournalType::Correction, 'allocations' => JournalType::Allocation, 'accruals' => JournalType::Accrual, 'prepayments' => JournalType::Prepayment];
        try {
            return new JournalResource($create->handle($map[$type], collect($data)->except('lines')->all(), $data['lines']));
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['lines' => $e->getMessage()]);
        }
    }

    public function post(Request $request, string $journal, PostJournal $post): JournalResource
    {
        $model = JournalEntry::findOrFail($journal);
        Gate::authorize('update', $model);
        try {
            $actor = $request->user()?->getAuthIdentifier();

            return new JournalResource($post->handle($model, $actor === null ? null : (string) $actor));
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }
    }

    public function reverse(Request $request, string $journal, ReverseJournal $reverse): JournalResource
    {
        $model = JournalEntry::findOrFail($journal);
        Gate::authorize('update', $model);
        try {
            $actor = $request->user()?->getAuthIdentifier();

            return new JournalResource($reverse->handle($model, ['description' => $request->input('description')], $actor === null ? null : (string) $actor));
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }
    }

    public function balances(Request $request, AccountBalances $balances)
    {
        $data = $request->validate(['book_id' => ['required', 'integer'], 'through' => ['nullable', 'date']]);
        Gate::authorize('viewAny', JournalEntry::class);

        return response()->json(['data' => $balances->handle((int) $data['book_id'], $data['through'] ?? null)]);
    }

    public function saveRecurring(Request $request, SaveRecurringJournal $save)
    {
        Gate::authorize('create', JournalEntry::class);
        $data = $request->validate(['book_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:160'], 'frequency' => ['required', 'in:daily,weekly,monthly,quarterly,yearly'], 'next_run_on' => ['required', 'date'], 'end_on' => ['nullable', 'date', 'after_or_equal:next_run_on'], 'description' => ['nullable', 'string'], 'lines' => ['required', 'array', 'min:2'], 'lines.*.account_id' => ['required', 'integer'], 'lines.*.debit' => ['nullable', 'numeric', 'min:0'], 'lines.*.credit' => ['nullable', 'numeric', 'min:0']]);
        try {
            return response()->json(['data' => $save->handle($data)], 201);
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['lines' => $e->getMessage()]);
        }
    }

    public function generateRecurring(Request $request, string $recurring, GenerateRecurringJournal $generate): JournalResource
    {
        $template = RecurringJournal::findOrFail($recurring);
        Gate::authorize('create', JournalEntry::class);
        try {
            $actor = $request->user()?->getAuthIdentifier();

            return new JournalResource($generate->handle($template, $actor === null ? null : (string) $actor));
        } catch (InvalidJournal $e) {
            throw ValidationException::withMessages(['recurring' => $e->getMessage()]);
        }
    }

    public function destroy(string $journal): Response
    {
        $model = JournalEntry::findOrFail($journal);
        Gate::authorize('delete', $model);
        abort_if($model->status->value !== 'draft', 409, 'Only draft journals may be deleted.');
        $model->delete();

        return response()->noContent();
    }
}
