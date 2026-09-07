<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Events\AccountingSettingSaved;

final class SaveAccountingSetting
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param class-string<Model> $modelClass @param array<string,mixed> $attributes */
    public function handle(string $modelClass, ?Model $setting, array $attributes): Model
    {
        return DB::transaction(function () use ($modelClass, $setting, $attributes): Model {
            $setting ??= new $modelClass();
            $setting->fill($attributes);
            $setting->save();
            DB::afterCommit(fn () => $this->events->dispatch(new AccountingSettingSaved($setting)));

            return $setting->refresh();
        });
    }
}
