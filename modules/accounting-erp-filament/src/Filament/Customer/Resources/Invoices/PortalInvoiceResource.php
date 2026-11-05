<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Customer\Resources\Invoices;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\ErpFilament\Filament\Customer\Resources\Invoices\Pages\ListPortalInvoices;

/**
 * The customer's own invoices — read-only. Every query is scoped to the logged-in
 * customer; there is no create/edit/delete path.
 */
class PortalInvoiceResource extends Resource
{
    #[\Override]
    protected static ?string $model = Invoice::class;

    protected static bool $isScopedToTenant = false;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    #[\Override]
    protected static ?string $navigationLabel = 'My Invoices';

    #[\Override]
    protected static ?string $modelLabel = 'invoice';

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customer_id', Auth::guard('customer')->id());
    }

    #[\Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->searchable()->weight(FontWeight::Bold),
                TextColumn::make('invoice_date')->date()->sortable(),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('total_amount')->money()->sortable(),
                TextColumn::make('payment_status')->badge(),
            ])
            ->defaultSort('invoice_date', 'desc')
            ->recordActions([
                Action::make('download')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Invoice $record) {
                        // Defence in depth: the record already comes from the
                        // customer-scoped query, but generatePDF() is shared with
                        // staff resources, so re-verify ownership here too.
                        abort_unless($record->customer_id === Auth::guard('customer')->id(), 403);

                        return $record->generatePDF();
                    }),
            ])
            ->toolbarActions([]);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListPortalInvoices::route('/'),
        ];
    }
}
