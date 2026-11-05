<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ReminderSettings;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\ReminderSetting;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ReminderSettings\Pages\EditReminderSetting;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ReminderSettings\Pages\ListReminderSettings;

class ReminderSettingResource extends Resource
{
    #[\Override]
    protected static ?string $model = ReminderSetting::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    #[\Override]
    protected static ?string $navigationLabel = 'Reminder Settings';

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    #[\Override]
    protected static ?int $navigationSort = 1;

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('days_before_reminder')
                    ->label('Days Before First Reminder')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Number of days after invoice date before sending first reminder'),

                TextInput::make('reminder_frequency_days')
                    ->label('Days Between Reminders')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Number of days to wait between reminders'),

                TextInput::make('max_reminders')
                    ->label('Maximum Number of Reminders')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Maximum number of reminders to send per invoice'),

                Toggle::make('is_active')
                    ->label('Enable Reminders')
                    ->required()
                    ->helperText('Toggle the reminder system on/off'),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('days_before_reminder')
                    ->label('Days Before First Reminder')
                    ->sortable(),
                TextColumn::make('reminder_frequency_days')
                    ->label('Reminder Frequency (Days)')
                    ->sortable(),
                TextColumn::make('max_reminders')
                    ->label('Max Reminders')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListReminderSettings::route('/'),
            'edit' => EditReminderSetting::route('/{record}/edit'),
        ];
    }
}
