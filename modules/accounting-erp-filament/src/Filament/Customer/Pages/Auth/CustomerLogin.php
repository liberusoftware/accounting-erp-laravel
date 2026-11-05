<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Customer\Pages\Auth;

use Filament\Auth\Pages\Login;

/**
 * The customer's email lives in `customer_email`, not `email`, so map the
 * login form's email field onto the real column before the guard looks it up.
 */
class CustomerLogin extends Login
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[\Override]
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'customer_email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
