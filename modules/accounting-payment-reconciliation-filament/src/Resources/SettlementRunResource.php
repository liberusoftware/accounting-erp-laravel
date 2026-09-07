<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class SettlementRunResource extends Resource
{
    protected static ?string $model = SettlementRun::class;

    protected static ?string $navigationLabel = 'Payment Reconciliation';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('provider')->required(), TextInput::make('settlement_ref')->required(), TextInput::make('merchant_ref'), TextInput::make('currency')->required()->length(3), TextInput::make('period_start')->required(), TextInput::make('period_end')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('provider')->searchable(), TextColumn::make('settlement_ref')->searchable(), TextColumn::make('currency'), TextColumn::make('net_amount')->money(fn ($record) => $record->currency), TextColumn::make('status')->badge(), TextColumn::make('items_count')->counts('items'), TextColumn::make('created_at')->dateTime()->sortable()])->filters([SelectFilter::make('status')->options(['imported' => 'Imported', 'partially_matched' => 'Partially matched', 'matched' => 'Matched', 'exception' => 'Exception', 'reconciled' => 'Reconciled'])])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSettlementRuns::route('/')];
    }
}
