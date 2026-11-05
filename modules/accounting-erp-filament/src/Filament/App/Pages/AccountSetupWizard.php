<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

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

    protected string $view = 'accounting-erp-filament::filament.app.pages.account-setup-wizard';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var list<string> */
    public array $configuredIntegrations = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();
        $team = Filament::getTenant() ?? $user?->currentTeam;

        return $user instanceof User && $team instanceof Team && $user->ownsTeam($team);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
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

    public function getSubheading(): ?string
    {
        return 'Set up your business and reporting preferences. Connections and messaging are optional, and you can update them later.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Business profile')
                        ->icon('heroicon-o-building-office')
                        ->description('Start with your business identity.')
                        ->schema([
                            TextInput::make('business_name')->label('Business or team name')->required()->maxLength(255),
                            Select::make('country')->options(['GB' => 'United Kingdom', 'US' => 'United States', 'CA' => 'Canada', 'AU' => 'Australia', 'IE' => 'Ireland'])->required()->native(false),
                        ])->columns(2),
                    Step::make('Accounting preferences')
                        ->icon('heroicon-o-book-open')
                        ->description('Choose how you organise your reporting.')
                        ->schema([
                            Select::make('currency')->label('Base currency')->helperText('Choose the currency you use to keep your books. Review this before posting transactions.')->options(['GBP' => 'GBP — Pound sterling', 'USD' => 'USD — US dollar', 'EUR' => 'EUR — Euro', 'CAD' => 'CAD — Canadian dollar', 'AUD' => 'AUD — Australian dollar'])->required()->native(false),
                            Select::make('fiscal_year_start')->label('Fiscal year starts')->helperText('The first month of your annual reporting period.')->options(collect(range(1, 12))->mapWithKeys(fn (int $month): array => [sprintf('%02d-01', $month) => Carbon::create(2000, $month, 1)->format('F')])->all())->required()->native(false),
                            Select::make('timezone')->label('Timezone')->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))->searchable()->required()->rule('timezone')->helperText('Used for your workspace reporting preferences.'),
                        ])->columns(2),
                    Step::make('Connections')
                        ->icon('heroicon-o-link')
                        ->description('Optional — you can connect services later.')
                        ->schema([
                            Section::make('Bank feeds · Plaid')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.plaid.client_id')->label('Plaid client ID')->helperText('Required for bank feeds.')->maxLength(255),
                                TextInput::make('integrations.plaid.secret')->label('Plaid secret')->password()->revealable()->maxLength(255),
                                TextInput::make('integrations.plaid.webhook_verification_key')->label('Plaid webhook verification key')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Import · QuickBooks')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.qbo.client_id')->label('QuickBooks client ID')->maxLength(255),
                                TextInput::make('integrations.qbo.client_secret')->label('QuickBooks client secret')->password()->revealable()->maxLength(255),
                                TextInput::make('integrations.qbo.webhook_verifier_token')->label('QuickBooks webhook verifier token')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Import · Xero')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.xero.client_id')->label('Xero client ID')->maxLength(255),
                                TextInput::make('integrations.xero.client_secret')->label('Xero client secret')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Import · Sage')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.sage.client_id')->label('Sage client ID')->maxLength(255),
                                TextInput::make('integrations.sage.client_secret')->label('Sage client secret')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Tax · HMRC')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.hmrc.client_id')->label('HMRC client ID')->maxLength(255),
                                TextInput::make('integrations.hmrc.client_secret')->label('HMRC client secret')->password()->revealable()->maxLength(255),
                                TextInput::make('integrations.hmrc.server_token')->label('HMRC server token')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Banking · Revolut')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.revolut.client_id')->label('Revolut client ID')->maxLength(255),
                                TextInput::make('integrations.revolut.client_secret')->label('Revolut client secret')->password()->revealable()->maxLength(255),
                                TextInput::make('integrations.revolut.webhook_secret')->label('Revolut webhook secret')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Banking · Wise')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.wise.client_id')->label('Wise client ID')->maxLength(255),
                                TextInput::make('integrations.wise.client_secret')->label('Wise client secret')->password()->revealable()->maxLength(255),
                                TextInput::make('integrations.wise.webhook_public_key')->label('Wise webhook public key')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                            Section::make('Exchange rates')->description('Optional. Leave blank to keep saved credentials.')->collapsible()->collapsed()->schema([
                                TextInput::make('integrations.exchange_rate_api.key')->label('Exchange-rate API key')->password()->revealable()->maxLength(255),
                            ])->columns(2),
                        ])->columns(2),
                    Step::make('Team settings')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->description('Add optional messaging credentials and keep your workspace secure.')
                        ->schema([
                            TextInput::make('vonage_key')->label('Vonage API key')->password()->revealable(false)->helperText('Optional. Used for SMS notifications.'),
                            TextInput::make('vonage_secret')->label('Vonage API secret')->password()->revealable(false),
                            TextInput::make('vonage_from')->label('SMS sender ID or number')->maxLength(255),
                        ])->columns(2),
                    Step::make('Ready to go')
                        ->icon('heroicon-o-check-badge')
                        ->description('Review the setup and finish when you are ready.')
                        ->schema([
                            Text::make(fn (Get $get): string => 'Business: '.($get('business_name') ?? '')),
                            Text::make(fn (Get $get): string => 'Base currency: '.($get('currency') ?? '')),
                            Text::make(fn (Get $get): string => 'Financial year starts: '.(collect(range(1, 12))->mapWithKeys(fn (int $month): array => [sprintf('%02d-01', $month) => Carbon::create(2000, $month, 1)->format('j F')])->get($get('fiscal_year_start'), 'Not selected'))),
                            Text::make(fn (Get $get): string => 'Timezone: '.($get('timezone') ?? '')),
                            Section::make('Before your first transaction')->description('Saving this wizard stores your preferences. Complete these accounting tasks from your workspace before you start posting.')->schema([
                                Text::make('1. Review your chart of accounts with your accountant.'),
                                Text::make('2. Enter and check your opening balances.'),
                                Text::make('3. Connect your bank accounts when you are ready to import transactions.'),
                            ]),
                        ]),
                ])->persistStepInQueryString('setup-step')
                    ->submitAction(view('accounting-erp-filament::filament.app.pages.setup-submit')),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('form'),
        ]);
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);
        $state = $this->form->getState();
        $team = $this->team();
        $existingIntegrations = is_array($team->accounting_setup['integrations'] ?? null)
            ? $team->accounting_setup['integrations']
            : [];

        $team->forceFill([
            'name' => $state['business_name'],
            'accounting_setup' => array_replace($team->accounting_setup ?? [], [
                'country' => $state['country'],
                'currency' => $state['currency'],
                'fiscal_year_start' => $state['fiscal_year_start'],
                'timezone' => $state['timezone'],
                'integrations' => $this->mergeIntegrationCredentials(
                    $existingIntegrations,
                    is_array($state['integrations'] ?? null) ? $state['integrations'] : [],
                ),
            ]),
            'vonage_from' => $state['vonage_from'] ?? null,
            'accounting_setup_completed_at' => $team->accounting_setup_completed_at ?? now(),
        ]);

        if (filled($state['vonage_key'] ?? null)) {
            $team->vonage_key = $state['vonage_key'];
        }

        if (filled($state['vonage_secret'] ?? null)) {
            $team->vonage_secret = $state['vonage_secret'];
        }

        $team->save();

        Notification::make()->title('Workspace setup saved')->body('Your team settings and integration credentials are encrypted. You can connect providers from the relevant Banking or Accounting screens.')->success()->send();

        $this->redirect('/app');
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
