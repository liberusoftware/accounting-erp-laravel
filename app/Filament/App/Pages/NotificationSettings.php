<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\UserNotificationPreference;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Self-service page: the current user edits their own notification
 * channel preferences (mail / database / sms) and SMS phone number.
 *
 * @property-read Schema $form
 */
class NotificationSettings extends Page
{
    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    #[\Override]
    protected static ?string $title = 'Notification Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Account';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $preference = Auth::user()?->notificationPreference;

        $this->form->fill([
            'phone' => $preference?->phone,
            'mail_enabled' => $preference->mail_enabled ?? true,
            'database_enabled' => $preference->database_enabled ?? true,
            'sms_enabled' => $preference->sms_enabled ?? false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phone')
                    ->label('Phone number')
                    ->tel(),
                Toggle::make('mail_enabled')->label('Email notifications'),
                Toggle::make('database_enabled')->label('In-app notifications'),
                Toggle::make('sms_enabled')->label('SMS notifications'),
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
        $data = $this->data ?? [];

        UserNotificationPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'phone' => $data['phone'] ?? null,
                'mail_enabled' => (bool) ($data['mail_enabled'] ?? false),
                'database_enabled' => (bool) ($data['database_enabled'] ?? false),
                'sms_enabled' => (bool) ($data['sms_enabled'] ?? false),
            ],
        );

        Notification::make()->title('Notification settings saved')->success()->send();
    }
}
