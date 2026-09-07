<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesFilament\Resources;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\BankRules\Models\BankRule;

final class BankRuleResource extends Resource
{
    protected static ?string $model = BankRule::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Bank Rules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(160),
            TextInput::make('priority')->numeric()->minValue(0)->default(0)->required(),
            Select::make('automation_mode')->options(['disabled' => 'Disabled', 'suggest' => 'Suggest', 'automatic' => 'Automatic'])->required()->default('suggest'),
            Checkbox::make('enabled')->default(true),
            TextInput::make('conditions.text')->label('Text contains')->maxLength(180),
            TextInput::make('conditions.payee')->label('Payee contains')->maxLength(180),
            TextInput::make('conditions.amount_min')->numeric()->label('Minimum amount'),
            TextInput::make('conditions.amount_max')->numeric()->label('Maximum amount'),
            TextInput::make('conditions.account_id')->label('Account identifier'),
            TextInput::make('actions.category')->label('Category'),
            TextInput::make('actions.tax_code')->label('Tax code'),
            TextInput::make('actions.dimension_id')->label('Dimension identifier'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('priority')->sortable(),
            TextColumn::make('automation_mode')->badge(),
            IconColumn::make('enabled')->boolean(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('priority', 'desc');
    }
}
