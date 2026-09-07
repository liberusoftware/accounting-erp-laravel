<?php

declare(strict_types=1);

use App\Http\Controllers\PortalAccessController;
use App\Http\Controllers\SaasPremiumBillingController;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('home'))->name('home');

Route::middleware('auth')->prefix('billing/premium')->name('billing.premium')->group(function (): void {
    Route::get('/', [SaasPremiumBillingController::class, 'show'])->name('');
    Route::post('/checkout', [SaasPremiumBillingController::class, 'checkout'])->name('.checkout');
    Route::get('/success', [SaasPremiumBillingController::class, 'success'])->name('.success');
    Route::post('/portal', [SaasPremiumBillingController::class, 'portal'])->name('.portal');
});

Route::post('/stripe/webhook', [SaasPremiumBillingController::class, 'webhook'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('stripe.webhook');

Route::middleware('auth')->get('/dashboard', function (): RedirectResponse {
    $user = request()->user();

    if ($user->hasRoleInAnyTeam('super_admin')) {
        $team = $user->getDefaultTenant(Filament::getPanel('admin'));

        return redirect()->to('/admin/'.($team?->getKey() ?? ''));
    }

    if (($team = $user->currentTeam) !== null && $team->accounting_setup_completed_at === null) {
        return redirect('/app/account-setup');
    }

    return redirect()->route('filament.app.pages.dashboard');
})->name('dashboard');

// Portal access (customer + vendor): signed-link set-password + forgot. The
// guard is fixed per route via defaults('guard', ...) — never from user input.
foreach (['customer' => 'portal', 'vendor' => 'vendor-portal'] as $portalGuard => $portalPath) {
    Route::prefix($portalPath)->name("portal.{$portalGuard}.")->group(function () use ($portalGuard): void {
        Route::get('set-password/{id}', [PortalAccessController::class, 'showSetPassword'])
            ->defaults('guard', $portalGuard)->middleware('signed')->name('set-password');
        Route::post('set-password/{id}', [PortalAccessController::class, 'setPassword'])
            ->defaults('guard', $portalGuard)->middleware('signed')->name('set-password.store');
        Route::get('forgot', [PortalAccessController::class, 'showForgot'])
            ->defaults('guard', $portalGuard)->name('forgot');
        Route::post('forgot', [PortalAccessController::class, 'sendForgot'])
            ->defaults('guard', $portalGuard)->middleware('throttle:6,1')->name('forgot.send');
    });
}
