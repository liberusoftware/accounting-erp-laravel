<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Subscriptions;

use App\Filament\App\Resources\Subscriptions\Pages\CreateSubscription;
use App\Filament\App\Resources\Subscriptions\Pages\EditSubscription;
use App\Filament\App\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Workspace & Integrations';

    #[\Override]
    protected static ?string $model = Subscription::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    #[\Override]
    protected static ?string $navigationLabel = 'Subscriptions';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('customer_id')
                ->relationship('customer', 'customer_name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('plan_id')
                ->relationship('plan', 'name')
                ->required()
                ->searchable()
                ->preload(),

            DatePicker::make('next_billing_date'),

            // Status is system-managed (pause/resume/cancel actions), not
            // hand-editable — else a cancelled subscription could be flipped
            // back to active without going through the model's lifecycle methods.
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'paused' => 'Paused',
                    'cancelled' => 'Cancelled',
                    'expired' => 'Expired',
                ])
                ->disabled()
                ->dehydrated(false),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'danger' => 'cancelled',
                        'secondary' => 'expired',
                    ]),

                TextColumn::make('next_billing_date')
                    ->date()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('pause')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === 'active')
                    ->action(fn (Subscription $record) => $record->pause()),
                Action::make('resume')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === 'paused')
                    ->action(fn (Subscription $record) => $record->resume()),
                Action::make('cancel')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => in_array($record->status, ['active', 'paused'], true))
                    ->action(fn (Subscription $record) => $record->cancel()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'edit' => EditSubscription::route('/{record}/edit'),
        ];
    }
}
