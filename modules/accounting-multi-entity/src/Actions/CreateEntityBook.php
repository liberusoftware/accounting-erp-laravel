<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MultiEntity\Enums\EntityBookStatus;
use Liberu\Accounting\MultiEntity\Events\EntityBookActivated;
use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;

final class CreateEntityBook
{
    public function handle(array $attributes): EntityBook
    {
        foreach (['entity_ref', 'code', 'name', 'base_currency'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidEntity("Entity field [{$key}] is required.");
            }
        }$currency = strtoupper((string) $attributes['base_currency']);
        if (! preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidEntity('Base currency must be an ISO 4217 code.');
        }

        return DB::transaction(function () use ($attributes, $currency): EntityBook {
            $book = EntityBook::query()->firstOrCreate(['team_id' => $attributes['team_id'] ?? null, 'entity_ref' => $attributes['entity_ref']], ['code' => $attributes['code'], 'name' => $attributes['name'], 'base_currency' => $currency, 'timezone' => $attributes['timezone'] ?? 'UTC', 'tax_registration' => $attributes['tax_registration'] ?? null, 'status' => EntityBookStatus::Active, 'metadata' => $attributes['metadata'] ?? null]);
            if ($book->wasRecentlyCreated) {
                DB::afterCommit(fn () => event(new EntityBookActivated($book->refresh())));
            }

            return $book;
        });
    }
}
