<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Edits the current tenant team's Vonage SMS-sending credentials.
 * Team-admin only. Secrets are write-only: blank field = keep existing.
 */
/**
 * @property-read Schema $form
 */
class TeamNotificationSettings extends Page
{
    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    #[\Override]
    protected static ?string $title = 'SMS Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Workspace & Integrations';

    protected static ?int $navigationSort = 3;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();
        $tenant = Filament::getTenant();

        return $user instanceof User && $tenant instanceof Team && $user->ownsTeam($tenant);
    }

    public function mount(): void
    {
        $team = Filament::getTenant();

        // Never re-show plaintext key/secret; only the non-secret sender id.
        $this->form->fill([
            'vonage_from' => $team instanceof Team ? $team->vonage_from : null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vonage_key')
                    ->label('Vonage API Key')
                    ->password()
                    ->revealable(false)
                    ->helperText('Leave blank to keep the current key.'),
                TextInput::make('vonage_secret')
                    ->label('Vonage API Secret')
                    ->password()
                    ->revealable(false)
                    ->helperText('Leave blank to keep the current secret.'),
                TextInput::make('vonage_from')
                    ->label('Sender ID / From Number'),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                EmbeddedSchema::make('form'),
                Actions::make([
                    Action::make('save')
                        ->label('Save')
                        ->action(fn () => $this->save()),
                ]),
            ]),
        ]);
    }

    public function save(): void
    {
        $team = Filament::getTenant();

        if (! $team instanceof Team) {
            return;
        }

        $data = $this->data ?? [];

        $attributes = ['vonage_from' => $data['vonage_from'] ?? null];

        // Only overwrite secrets when a new value was actually entered.
        if (filled($data['vonage_key'] ?? null)) {
            $attributes['vonage_key'] = $data['vonage_key'];
        }

        if (filled($data['vonage_secret'] ?? null)) {
            $attributes['vonage_secret'] = $data['vonage_secret'];
        }

        $team->update($attributes);

        Notification::make()->title('SMS settings saved')->success()->send();
    }
}
