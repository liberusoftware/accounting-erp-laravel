<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\Core\Models\Book;

final readonly class BookCreated
{
    use Dispatchable;

    public function __construct(public Book $book) {}
}
