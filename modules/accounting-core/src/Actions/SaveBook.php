<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Events\BookCreated;
use Liberu\Accounting\Core\Models\Book;

final class SaveBook
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string,mixed> $attributes */
    public function handle(?Book $book, array $attributes): Book
    {
        return DB::transaction(function () use ($book, $attributes): Book {
            $wasRecentlyCreated = $book === null;
            $book ??= new Book();
            $book->fill($attributes);
            $book->save();
            if ($wasRecentlyCreated) {
                DB::afterCommit(fn () => $this->events->dispatch(new BookCreated($book)));
            }

            return $book->refresh();
        });
    }
}
