<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Events\MasterRecordCreated;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;

final class SaveReferenceData
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param class-string<Model> $modelClass @param array<string, mixed> $attributes */
    public function handle(string $modelClass, array $attributes, ?Model $record = null): Model
    {
        return DB::transaction(function () use ($modelClass, $attributes, $record): Model {
            $record ??= new $modelClass();
            $created = ! $record->exists;
            $legalEntityId = $attributes['legal_entity_id'] ?? $record->legal_entity_id ?? null;
            $identityField = array_key_exists('sku', $attributes) ? 'sku' : (array_key_exists('code', $attributes) ? 'code' : null);
            if ($created && $identityField !== null && $legalEntityId !== null && $modelClass::query()->where('legal_entity_id', $legalEntityId)->where($identityField, $attributes[$identityField])->exists()) {
                throw new DuplicateMasterRecord('The reference is already in use for this legal entity.');
            }
            $record->fill($attributes);
            $record->save();
            if ($created) {
                $this->events->dispatch(new MasterRecordCreated($record));
            }

            return $record->refresh();
        });
    }
}
