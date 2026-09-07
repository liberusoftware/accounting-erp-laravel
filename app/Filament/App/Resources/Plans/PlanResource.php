<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Plans;

use App\Filament\App\Resources\Plans\Pages\CreatePlan;
use App\Filament\App\Resources\Plans\Pages\EditPlan;
use App\Filament\App\Resources\Plans\Pages\ListPlans;
use App\Models\Plan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Workspace & Integrations';

    #[\Override]
    protected static ?string $model = Plan::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[\Override]
    protected static ?string $navigationLabel = 'Plans';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required(),

            TextInput::make('amount')
                ->numeric()
                ->required(),

            TextInput::make('currency')
                ->default('USD')
                ->required(),

            Select::make('interval')
                ->options([
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                ])
                ->required(),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('interval')
                    ->badge(),
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}
