<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\TaxCore\Models\TaxRule;
use Liberu\Accounting\TaxCoreFilament\Resources\TaxRuleResource\Pages\ListTaxRules;

final class TaxRuleResource extends Resource
{
    protected static ?string $model = TaxRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('code')->required(), TextInput::make('name')->required(), TextInput::make('tax_type')->required(), TextInput::make('jurisdiction_code'), TextInput::make('rate')->numeric()->required(), TextInput::make('effective_from')->type('date')->required(), TextInput::make('effective_until')->type('date'), TextInput::make('control_account_code'), TextInput::make('rounding_scale')->numeric()->default(2)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code')->searchable(), TextColumn::make('name'), TextColumn::make('tax_type'), TextColumn::make('jurisdiction_code'), TextColumn::make('rate'), TextColumn::make('status')->badge(), TextColumn::make('effective_from')->date()])->defaultSort('code');
    }

    public static function getPages(): array
    {
        return ['index' => ListTaxRules::route('/')];
    }
}
