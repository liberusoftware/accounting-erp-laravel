<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;

final class WithholdingTaxRuleResource extends Resource
{
    protected static ?string $model = WithholdingTaxRule::class;

    protected static ?string $navigationLabel = 'Withholding tax rules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('code')->required(), TextInput::make('name')->required(), TextInput::make('jurisdiction')->required(), TextInput::make('rate')->numeric()->required(), TextInput::make('threshold')->numeric(), TextInput::make('effective_from')->required(), TextInput::make('effective_until')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code')->searchable(), TextColumn::make('name'), TextColumn::make('jurisdiction'), TextColumn::make('rate'), TextColumn::make('status')->badge(), TextColumn::make('effective_from')->date()->sortable()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListWithholdingTaxRules::route('/')];
    }
}
