<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RevenueSchedules;

use App\Filament\App\Resources\RevenueSchedules\Pages\CreateRevenueSchedule;
use App\Filament\App\Resources\RevenueSchedules\Pages\EditRevenueSchedule;
use App\Filament\App\Resources\RevenueSchedules\Pages\ListRevenueSchedules;
use App\Models\RevenueSchedule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevenueScheduleResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    #[\Override]
    protected static ?string $model = RevenueSchedule::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    #[\Override]
    protected static ?string $navigationLabel = 'Revenue Schedules';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('invoice_id')
                ->relationship('invoice', 'invoice_number')
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('periods')
                ->numeric()
                ->integer()
                ->required()
                ->minValue(1),

            Select::make('deferred_account_id')
                ->relationship('deferredAccount', 'account_name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('revenue_account_id')
                ->relationship('revenueAccount', 'account_name')
                ->required()
                ->searchable()
                ->preload(),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('periods')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'secondary' => 'completed',
                    ]),

                TextColumn::make('recognized_progress')
                    ->label('Recognized')
                    ->getStateUsing(fn (RevenueSchedule $record): string => $record->entries()->where('recognized', true)->count().' / '.$record->periods),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListRevenueSchedules::route('/'),
            'create' => CreateRevenueSchedule::route('/create'),
            'edit' => EditRevenueSchedule::route('/{record}/edit'),
        ];
    }
}
