<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Events\NumberingSequenceCreated;
use Liberu\Accounting\Core\Models\NumberingSequence;

final class SaveNumberingSequence
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string,mixed> $attributes */
    public function handle(?NumberingSequence $sequence, array $attributes): NumberingSequence
    {
        return DB::transaction(function () use ($sequence, $attributes): NumberingSequence {
            $wasRecentlyCreated = $sequence === null;
            $sequence ??= new NumberingSequence();
            $sequence->fill($attributes);
            $sequence->save();
            if ($wasRecentlyCreated) {
                DB::afterCommit(fn () => $this->events->dispatch(new NumberingSequenceCreated($sequence)));
            }

            return $sequence->refresh();
        });
    }
}
