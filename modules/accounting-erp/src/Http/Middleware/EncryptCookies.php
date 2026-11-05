<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    #[\Override]
    protected $except = [
        //
    ];
}
