<?php

use Liberu\Accounting\ErpHttp\Http\Controllers\LoginController;

it('renders the canonical authentication view from the legacy login controller', function (): void {
    $view = app(LoginController::class)->showLoginForm();

    expect($view->name())->toBe('auth.login');
});
