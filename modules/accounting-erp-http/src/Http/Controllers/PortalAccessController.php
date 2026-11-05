<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Vendor;
use Liberu\Accounting\Erp\Notifications\PortalAccessNotification;

/**
 * Signed-link set-password + forgot-password flow shared by both portals. The
 * guard ('customer' | 'vendor') comes from the route default, never user input.
 */
class PortalAccessController extends Controller
{
    /** @var array<string, class-string<Customer|Vendor>> */
    private const MODELS = ['customer' => Customer::class, 'vendor' => Vendor::class];

    /** Email column per guard (Customer's is non-standard). */
    private const EMAIL_COLUMN = ['customer' => 'customer_email', 'vendor' => 'email'];

    public function showSetPassword(Request $request, int $id): View
    {
        $guard = $this->guard($request);
        $model = $this->modelClass($guard)::findOrFail($id);
        $this->assertLinkFresh($request, $model);

        return view('accounting-erp-http::portal.set-password', ['action' => $request->fullUrl()]);
    }

    public function setPassword(Request $request, int $id): RedirectResponse
    {
        $guard = $this->guard($request);
        $model = $this->modelClass($guard)::findOrFail($id);
        $this->assertLinkFresh($request, $model);

        $request->validate(['password' => ['required', 'confirmed', Password::defaults()]]);

        $model->password = $request->string('password')->value();
        $model->save();

        return redirect(Filament::getPanel($guard)->getLoginUrl())
            ->with('status', 'Password set — please log in.');
    }

    public function showForgot(Request $request): View
    {
        $guard = $this->guard($request);

        return view('accounting-erp-http::portal.forgot', ['action' => route("portal.{$guard}.forgot.send")]);
    }

    public function sendForgot(Request $request): RedirectResponse
    {
        $guard = $this->guard($request);
        $request->validate(['email' => ['required', 'email']]);

        $model = $this->modelClass($guard)::query()
            ->where(self::EMAIL_COLUMN[$guard], $request->string('email')->value())
            ->first();

        // Send only for a real record, but always return the same message so a
        // caller can't probe which emails exist (no account enumeration).
        $model?->notify(new PortalAccessNotification($guard));

        return back()->with('status', 'If that email is on file, a set-password link has been sent.');
    }

    /**
     * The link carries sha1 of the password as it was when minted. Once a
     * password is set that hash changes, so a reused or leaked link 403s —
     * effectively single-use (mirrors Laravel's signed email-verification).
     */
    private function assertLinkFresh(Request $request, Customer|Vendor $model): void
    {
        abort_unless(
            hash_equals((string) $request->query('hash'), sha1((string) $model->getAttribute('password'))),
            403,
        );
    }

    private function guard(Request $request): string
    {
        $guard = (string) $request->route('guard');
        abort_unless(array_key_exists($guard, self::MODELS), 404);

        return $guard;
    }

    /**
     * @return class-string<Customer|Vendor>
     */
    private function modelClass(string $guard): string
    {
        return self::MODELS[$guard];
    }
}
