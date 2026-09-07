<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationLabel = 'Estimates and quotes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('quote_ref')->required(), TextInput::make('customer_ref')->required(), TextInput::make('name')->required(), TextInput::make('legal_entity_id')->numeric()->required(), TextInput::make('currency')->required()->length(3), DatePicker::make('issue_date')->required(), DatePicker::make('expires_on'), Textarea::make('terms')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('quote_ref')->searchable(), TextColumn::make('customer_ref'), TextColumn::make('name'), TextColumn::make('status')->badge(), TextColumn::make('currency'), TextColumn::make('expires_on')->date(), TextColumn::make('version')])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEstimates::route('/')];
    }
}
