<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\Core\Actions\SaveAccountingSetting;
use Liberu\Accounting\Core\Models\AccountingDefault;
use Liberu\Accounting\Core\Models\AccountingPolicy;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\CoreApi\Http\Resources\AccountingSettingResource;

final class SettingsController extends Controller
{
    /** @return class-string<AccountingDefault|AccountingPolicy> */
    private function modelClass(string $setting): string
    {
        abort_unless(in_array($setting, ['defaults', 'policies'], true), 404);

        return $setting === 'defaults' ? AccountingDefault::class : AccountingPolicy::class;
    }

    public function index(Request $request, string $book, string $setting)
    {
        $bookModel = Book::findOrFail($book);
        Gate::authorize('view', $bookModel);
        $class = $this->modelClass($setting);

        return AccountingSettingResource::collection(
            $class::query()->where('book_id', $bookModel->getKey())->orderBy('key')->paginate(min($request->integer('per_page', 25), 100))
        );
    }

    public function store(Request $request, string $book, string $setting, SaveAccountingSetting $save): AccountingSettingResource
    {
        $bookModel = Book::findOrFail($book);
        Gate::authorize('update', $bookModel);
        $class = $this->modelClass($setting);
        $data = $request->validate(['key' => ['required', 'string', 'max:100'], 'value' => ['required', 'array']]);

        return new AccountingSettingResource($save->handle($class, null, $data + ['book_id' => $bookModel->getKey()]));
    }

    public function show(string $book, string $setting, string $record): AccountingSettingResource
    {
        $bookModel = Book::findOrFail($book);
        Gate::authorize('view', $bookModel);
        $model = $this->modelClass($setting)::query()->where('book_id', $bookModel->getKey())->findOrFail($record);

        return new AccountingSettingResource($model);
    }

    public function update(Request $request, string $book, string $setting, string $record, SaveAccountingSetting $save): AccountingSettingResource
    {
        $bookModel = Book::findOrFail($book);
        Gate::authorize('update', $bookModel);
        $model = $this->modelClass($setting)::query()->where('book_id', $bookModel->getKey())->findOrFail($record);
        $data = $request->validate(['key' => ['sometimes', 'required', 'string', 'max:100'], 'value' => ['sometimes', 'required', 'array']]);

        return new AccountingSettingResource($save->handle($model::class, $model, $data));
    }

    public function destroy(string $book, string $setting, string $record): Response
    {
        $bookModel = Book::findOrFail($book);
        Gate::authorize('update', $bookModel);
        $model = $this->modelClass($setting)::query()->where('book_id', $bookModel->getKey())->findOrFail($record);
        $model->delete();

        return response()->noContent();
    }
}
