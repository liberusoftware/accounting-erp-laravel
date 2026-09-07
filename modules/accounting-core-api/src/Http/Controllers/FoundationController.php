<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\Core\Actions\AllocateNumber;
use Liberu\Accounting\Core\Actions\SaveBook;
use Liberu\Accounting\Core\Actions\SaveFiscalCalendar;
use Liberu\Accounting\Core\Actions\SaveNumberingSequence;
use Liberu\Accounting\Core\Exceptions\InvalidFiscalCalendar;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\CoreApi\Http\Resources\BookResource;
use Liberu\Accounting\CoreApi\Http\Resources\FiscalCalendarResource;
use Liberu\Accounting\CoreApi\Http\Resources\NumberingSequenceResource;

final class FoundationController extends Controller
{
    public function books(Request $request)
    {
        Gate::authorize('viewAny', Book::class);

        return BookResource::collection(Book::query()->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function book(string $book): BookResource
    {
        $model = Book::findOrFail($book);
        Gate::authorize('view', $model);

        return new BookResource($model);
    }

    public function storeBook(Request $request, SaveBook $save): BookResource
    {
        Gate::authorize('create', Book::class);
        $data = $request->validate(['legal_entity_id' => 'required|integer|exists:accounting_legal_entities,id', 'name' => 'required|string|max:255', 'code' => 'required|string|max:50', 'accounting_basis' => 'required|in:accrual,cash', 'is_active' => 'sometimes|boolean']);

        return new BookResource($save->handle(null, $data));
    }

    public function calendars(Request $request, string $book)
    {
        $model = Book::findOrFail($book);
        Gate::authorize('view', $model);

        return FiscalCalendarResource::collection($model->fiscalCalendars()->paginate(100));
    }

    public function storeCalendar(Request $request, string $book, SaveFiscalCalendar $save): FiscalCalendarResource
    {
        $model = Book::findOrFail($book);
        Gate::authorize('update', $model);
        $data = $request->validate(['starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'is_closed' => 'sometimes|boolean']);
        try {
            return new FiscalCalendarResource($save->handle(null, $data + ['book_id' => $model->getKey()]));
        } catch (InvalidFiscalCalendar $exception) {
            throw ValidationException::withMessages(['starts_on' => $exception->getMessage()]);
        }
    }

    public function sequences(Request $request, string $book)
    {
        $model = Book::findOrFail($book);
        Gate::authorize('view', $model);

        return NumberingSequenceResource::collection($model->numberingSequences()->paginate(100));
    }

    public function storeSequence(Request $request, string $book, SaveNumberingSequence $save): NumberingSequenceResource
    {
        $model = Book::findOrFail($book);
        Gate::authorize('update', $model);
        $data = $request->validate(['key' => 'required|string|max:100', 'prefix' => 'nullable|string|max:30', 'next_number' => 'sometimes|integer|min:1', 'padding' => 'sometimes|integer|min:1|max:20']);

        return new NumberingSequenceResource($save->handle(null, $data + ['book_id' => $model->getKey()]));
    }

    public function allocateSequence(string $book, string $sequence, AllocateNumber $allocate): JsonResponse
    {
        $model = Book::findOrFail($book);
        Gate::authorize('update', $model);
        $record = $model->numberingSequences()->findOrFail($sequence);

        return response()->json(['data' => ['number' => $allocate->handle($record)]]);
    }
}
