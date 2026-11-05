<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Models\Vendor;
use Liberu\Accounting\Erp\Notifications\PortalAccessNotification;
use Tests\TestCase;

class PortalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_access_is_isolated_per_identity(): void
    {
        $customer = Customer::factory()->create();
        $vendor = Vendor::factory()->create();
        $staff = User::factory()->create();

        $this->assertTrue($customer->canAccessPanel(Filament::getPanel('customer')));
        $this->assertFalse($customer->canAccessPanel(Filament::getPanel('vendor')));
        $this->assertFalse($customer->canAccessPanel(Filament::getPanel('admin')));

        $this->assertTrue($vendor->canAccessPanel(Filament::getPanel('vendor')));
        $this->assertFalse($vendor->canAccessPanel(Filament::getPanel('customer')));

        // Staff must never reach the external portals.
        $this->assertFalse($staff->canAccessPanel(Filament::getPanel('customer')));
        $this->assertFalse($staff->canAccessPanel(Filament::getPanel('vendor')));
        $this->assertTrue($staff->canAccessPanel(Filament::getPanel('app')));
    }

    private function setPasswordUrl(Customer $customer): string
    {
        return URL::temporarySignedRoute('portal.customer.set-password', now()->addHour(), [
            'id' => $customer->id,
            'hash' => sha1((string) $customer->password),
        ]);
    }

    public function test_signed_link_sets_password_and_enables_login(): void
    {
        $customer = Customer::factory()->create();
        $url = $this->setPasswordUrl($customer);

        $this->get($url)->assertOk();
        $this->post($url, ['password' => 'Str0ng-Passw0rd!', 'password_confirmation' => 'Str0ng-Passw0rd!'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $customer->refresh();
        $this->assertTrue(Hash::check('Str0ng-Passw0rd!', (string) $customer->password));

        // Login uses the non-standard customer_email column via the guard.
        $this->assertTrue(
            auth()->guard('customer')->attempt(['customer_email' => $customer->customer_email, 'password' => 'Str0ng-Passw0rd!'])
        );
    }

    public function test_link_is_single_use_once_a_password_is_set(): void
    {
        $customer = Customer::factory()->create();
        $url = $this->setPasswordUrl($customer);

        $this->post($url, ['password' => 'Str0ng-Passw0rd!', 'password_confirmation' => 'Str0ng-Passw0rd!'])
            ->assertRedirect();

        // The same link now carries a stale password-hash → rejected (no takeover).
        $this->get($url)->assertForbidden();
        $this->post($url, ['password' => 'Attacker-Pw-99!', 'password_confirmation' => 'Attacker-Pw-99!'])
            ->assertForbidden();

        $customer->refresh();
        $this->assertTrue(Hash::check('Str0ng-Passw0rd!', (string) $customer->password));
    }

    public function test_expired_or_tampered_link_is_rejected(): void
    {
        $customer = Customer::factory()->create();

        $expired = URL::temporarySignedRoute('portal.customer.set-password', now()->subMinute(), ['id' => $customer->id]);
        $this->get($expired)->assertForbidden();

        $valid = URL::temporarySignedRoute('portal.customer.set-password', now()->addHour(), ['id' => $customer->id]);
        // Swap the id in the path — signature no longer matches.
        $tampered = str_replace('/set-password/'.$customer->id, '/set-password/'.($customer->id + 1), $valid);
        $this->get($tampered)->assertForbidden();
    }

    public function test_forgot_does_not_enumerate_accounts(): void
    {
        Notification::fake();
        $customer = Customer::factory()->create();

        $known = $this->post(route('portal.customer.forgot.send'), ['email' => $customer->customer_email]);
        $unknown = $this->post(route('portal.customer.forgot.send'), ['email' => 'nobody@example.com']);

        // Identical outcome regardless of whether the email exists.
        $known->assertRedirect();
        $unknown->assertRedirect();
        $this->assertSame($known->getSession()->get('status'), $unknown->getSession()->get('status'));

        // A link is sent only for the real record.
        Notification::assertSentTo($customer, PortalAccessNotification::class);
        Notification::assertSentTimes(PortalAccessNotification::class, 1);
    }
}
