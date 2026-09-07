<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Events\FiscalCalendarCreated;
use Liberu\Accounting\Core\Exceptions\InvalidFiscalCalendar;
use Liberu\Accounting\Core\Models\FiscalCalendar;

final class SaveFiscalCalendar
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array{book_id:int|string,starts_on:string,ends_on:string,is_closed?:bool} $attributes */
    public function handle(?FiscalCalendar $calendar, array $attributes): FiscalCalendar
    {
        if ($attributes['starts_on'] > $attributes['ends_on']) {
            throw new InvalidFiscalCalendar('Fiscal calendar start must not be after its end.');
        }

        return DB::transaction(function () use ($calendar, $attributes): FiscalCalendar {
            $query = FiscalCalendar::query()->where('book_id', $attributes['book_id'])
                ->where('starts_on', '<=', $attributes['ends_on'])->where('ends_on', '>=', $attributes['starts_on']);
            if ($calendar) {
                $query->where('id', '!=', $calendar->getKey());
            }
            if ($query->exists()) {
                throw new InvalidFiscalCalendar('Fiscal calendar periods may not overlap within a book.');
            }
            $wasRecentlyCreated = $calendar === null;
            $calendar ??= new FiscalCalendar();
            $calendar->fill($attributes)->save();
            if ($wasRecentlyCreated) {
                DB::afterCommit(fn () => $this->events->dispatch(new FiscalCalendarCreated($calendar)));
            }

            return $calendar->refresh();
        });
    }
}
