<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * First-use setup for a team. Integration credentials are team-scoped and
 * encrypted by the Team model; values are never displayed after saving.
 *
 * @property-read Schema $form
 */
class AccountSetupWizard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Workspace & Integrations';

    protected static ?string $navigationLabel = 'Account Setup';

    protected static ?string $title = 'Set up your accounting workspace';

    protected static ?string $slug = 'account-setup';

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.app.pages.account-setup-wizard';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var list<string> */
    public array $configuredIntegrations = [];

    public function mount(): void
    {
        $team = $this->team();
        $setup = $team->accounting_setup ?? [];
        $this->configuredIntegrations = collect($setup['integrations'] ?? [])
            ->filter(fn (mixed $credentials): bool => is_array($credentials) && collect($credentials)->filter()->isNotEmpty())
            ->keys()
            ->values()
            ->all();

        $this->form->fill([
            'business_name' => $team->name,
            'country' => $setup['country'] ?? 'GB',
            'currency' => $setup['currency'] ?? 'GBP',
            'fiscal_year_start' => $setup['fiscal_year_start'] ?? '01-01',
            'timezone' => $setup['timezone'] ?? config('app.timezone', 'UTC'),
            'vonage_from' => $team->vonage_from,
            // Integration credentials are write-only. Existing secrets are
            // merged on save and are never sent back to the browser.
            'integrations' => [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Business profile')
                        ->description('Tell us how to configure your books.')
                        ->schema([
                            TextInput::make('business_name')->label('Business or team name')->required()->maxLength(255),
                            Select::make('country')->options(['GB' => 'United Kingdom', 'US' => 'United States', 'CA' => 'Canada', 'AU' => 'Australia', 'IE' => 'Ireland'])->required()->native(false),
                            Select::make('currency')->options(['GBP' => 'GBP — Pound sterling', 'USD' => 'USD — US dollar', 'EUR' => 'EUR — Euro', 'CAD' => 'CAD — Canadian dollar', 'AUD' => 'AUD — Australian dollar'])->required()->native(false),
                            Select::make('fiscal_year_start')->label('Fiscal year starts')->options(collect(range(1, 12))->mapWithKeys(fn (int $month): array => [sprintf('%02d-01', $month) => Carbon::create()->month($month)->format('F')])->all())->required()->native(false),
                            TextInput::make('timezone')->label('Timezone')->required()->default(config('app.timezone', 'UTC')),
                        ])->columns(2),
                    Step::make('Connections')
                        ->description('Add credentials only for services you plan to use.')
                        ->schema([
                            TextInput::make('integrations.plaid.client_id')->label('Plaid client ID')->helperText('Required for bank feeds.')->maxLength(255),
                            TextInput::make('integrations.plaid.secret')->label('Plaid secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.plaid.webhook_verification_key')->label('Plaid webhook verification key')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.qbo.client_id')->label('QuickBooks client ID')->maxLength(255),
                            TextInput::make('integrations.qbo.client_secret')->label('QuickBooks client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.qbo.webhook_verifier_token')->label('QuickBooks webhook verifier token')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.xero.client_id')->label('Xero client ID')->maxLength(255),
                            TextInput::make('integrations.xero.client_secret')->label('Xero client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.sage.client_id')->label('Sage client ID')->maxLength(255),
                            TextInput::make('integrations.sage.client_secret')->label('Sage client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.hmrc.client_id')->label('HMRC client ID')->maxLength(255),
                            TextInput::make('integrations.hmrc.client_secret')->label('HMRC client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.hmrc.server_token')->label('HMRC server token')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.revolut.client_id')->label('Revolut client ID')->maxLength(255),
                            TextInput::make('integrations.revolut.client_secret')->label('Revolut client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.revolut.webhook_secret')->label('Revolut webhook secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.wise.client_id')->label('Wise client ID')->maxLength(255),
                            TextInput::make('integrations.wise.client_secret')->label('Wise client secret')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.wise.webhook_public_key')->label('Wise webhook public key')->password()->revealable()->maxLength(255),
                            TextInput::make('integrations.exchange_rate_api.key')->label('Exchange-rate API key')->password()->revealable()->maxLength(255),
                        ])->columns(2),
                    Step::make('Team settings')
                        ->description('Add optional messaging credentials and keep your workspace secure.')
                        ->schema([
                            TextInput::make('vonage_key')->label('Vonage API key')->password()->revealable(false)->helperText('Optional. Used for SMS notifications.'),
                            TextInput::make('vonage_secret')->label('Vonage API secret')->password()->revealable(false),
                            TextInput::make('vonage_from')->label('SMS sender ID or number')->maxLength(255),
                        ])->columns(2),
                    Step::make('Ready to go')
                        ->description('Review the setup and finish when you are ready.')
                        ->schema([
                            TextInput::make('setup_summary')->label('Next step')->default('Connect bank accounts and import your opening balances.')->disabled()->dehydrated(false),
                        ]),
                ])->persistStepInQueryString('setup-step'),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('form'),
            Actions::make([
                Action::make('finish')
                    ->label('Save setup')
                    ->color('primary')
                    ->action(function (): void {
                        $this->save();
                    }),
            ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $team = $this->team();
        $existingIntegrations = is_array($team->accounting_setup['integrations'] ?? null)
            ? $team->accounting_setup['integrations']
            : [];

        $team->forceFill([
            'name' => $state['business_name'],
            'accounting_setup' => [
                'country' => $state['country'],
                'currency' => $state['currency'],
                'fiscal_year_start' => $state['fiscal_year_start'],
                'timezone' => $state['timezone'],
                'integrations' => $this->mergeIntegrationCredentials(
                    $existingIntegrations,
                    is_array($state['integrations'] ?? null) ? $state['integrations'] : [],
                ),
            ],
            'vonage_from' => $state['vonage_from'] ?? null,
            'accounting_setup_completed_at' => now(),
        ])->save();

        if (filled($state['vonage_key'] ?? null)) {
            $team->vonage_key = $state['vonage_key'];
        }

        if (filled($state['vonage_secret'] ?? null)) {
            $team->vonage_secret = $state['vonage_secret'];
        }

        $team->save();

        Notification::make()->title('Workspace setup saved')->body('Your team settings and integration credentials are encrypted. You can connect providers from the relevant Banking or Accounting screens.')->success()->send();
    }

    /**
     * Keep existing credentials when a write-only field is left blank.
     *
     * @param  array<string, mixed>  $existing
     * @param  array<string, mixed>  $submitted
     * @return array<string, array<string, string>>
     */
    private function mergeIntegrationCredentials(array $existing, array $submitted): array
    {
        $allowed = [
            'plaid' => ['client_id', 'secret', 'webhook_verification_key'],
            'qbo' => ['client_id', 'client_secret', 'webhook_verifier_token'],
            'xero' => ['client_id', 'client_secret'],
            'sage' => ['client_id', 'client_secret'],
            'hmrc' => ['client_id', 'client_secret', 'server_token'],
            'revolut' => ['client_id', 'client_secret', 'webhook_secret'],
            'wise' => ['client_id', 'client_secret', 'webhook_public_key'],
            'exchange_rate_api' => ['key'],
        ];

        $merged = [];

        foreach ($allowed as $provider => $fields) {
            $credentials = is_array($existing[$provider] ?? null) ? $existing[$provider] : [];

            foreach ($fields as $field) {
                $value = Arr::get($submitted, $provider.'.'.$field);

                if (is_string($value) && trim($value) !== '') {
                    $credentials[$field] = trim($value);
                }
            }

            if ($credentials !== []) {
                $merged[$provider] = Arr::only($credentials, $fields);
            }
        }

        return $merged;
    }

    private function team(): Team
    {
        $team = Filament::getTenant() ?? Auth::user()?->currentTeam;
        abort_unless($team instanceof Team, 403);

        return $team;
    }
}
