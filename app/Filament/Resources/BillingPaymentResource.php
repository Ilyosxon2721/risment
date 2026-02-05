<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingPaymentResource\Pages;
use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillingPaymentResource extends Resource
{
    protected static ?string $model = BillingPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Платежи';

    protected static ?string $modelLabel = 'платёж';

    protected static ?string $pluralModelLabel = 'Платежи';

    protected static ?string $navigationGroup = 'Финансы';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('invoice_id', null)),

                        Forms\Components\Select::make('invoice_id')
                            ->label('Счёт')
                            ->options(function (Get $get): array {
                                $companyId = $get('company_id');
                                if (!$companyId) {
                                    return [];
                                }
                                return BillingInvoice::where('company_id', $companyId)
                                    ->whereIn('status', [
                                        BillingInvoice::STATUS_ISSUED,
                                        BillingInvoice::STATUS_PARTIALLY_PAID,
                                    ])
                                    ->orderByDesc('created_at')
                                    ->get()
                                    ->mapWithKeys(fn ($inv) => [
                                        $inv->id => "#{$inv->invoice_number} — " . number_format($inv->total) . " UZS ({$inv->period})"
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $invoice = BillingInvoice::find($state);
                                    if ($invoice) {
                                        // Calculate remaining amount
                                        $paid = BillingPayment::where('invoice_id', $state)
                                            ->where('status', BillingPayment::STATUS_COMPLETED)
                                            ->sum('amount');
                                        $remaining = $invoice->total - $paid;
                                        $set('amount', max(0, $remaining));
                                    }
                                }
                            }),

                        Forms\Components\Select::make('method')
                            ->label('Способ оплаты')
                            ->options(BillingPayment::getMethodOptions())
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Детали платежа')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('сум'),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Дата платежа')
                            ->default(now())
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(BillingPayment::getStatusOptions())
                            ->default(BillingPayment::STATUS_COMPLETED)
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\TextInput::make('external_ref')
                            ->label('Внешний ID / Референс')
                            ->maxLength(100)
                            ->placeholder('ID транзакции из платёжной системы'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечание')
                            ->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Счёт')
                    ->url(fn (BillingPayment $record): ?string =>
                        $record->invoice_id
                            ? BillingInvoiceResource::getUrl('view', ['record' => $record->invoice_id])
                            : null
                    )
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('UZS', locale: 'ru')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('UZS', locale: 'ru')),

                Tables\Columns\TextColumn::make('method')
                    ->label('Способ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BillingPayment::getMethodOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        BillingPayment::METHOD_PAYME => 'success',
                        BillingPayment::METHOD_CLICK => 'info',
                        BillingPayment::METHOD_TRANSFER => 'primary',
                        BillingPayment::METHOD_CASH => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BillingPayment::getStatusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        BillingPayment::STATUS_COMPLETED => 'success',
                        BillingPayment::STATUS_PENDING => 'warning',
                        BillingPayment::STATUS_FAILED => 'danger',
                        BillingPayment::STATUS_REFUNDED => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Дата платежа')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('external_ref')
                    ->label('Референс')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Создал')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Компания')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('method')
                    ->label('Способ')
                    ->options(BillingPayment::getMethodOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(BillingPayment::getStatusOptions()),

                Tables\Filters\Filter::make('paid_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('С'),
                        Forms\Components\DatePicker::make('until')
                            ->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('paid_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('refund')
                    ->label('Возврат')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Оформить возврат платежа?')
                    ->visible(fn (BillingPayment $record): bool =>
                        $record->status === BillingPayment::STATUS_COMPLETED)
                    ->action(function (BillingPayment $record): void {
                        $record->update(['status' => BillingPayment::STATUS_REFUNDED]);

                        // If linked to invoice, update invoice status
                        if ($record->invoice_id) {
                            $invoice = $record->invoice;
                            if ($invoice && $invoice->status === BillingInvoice::STATUS_PAID) {
                                $invoice->update(['status' => BillingInvoice::STATUS_ISSUED]);
                            }
                        }

                        Notification::make()
                            ->title('Платёж возвращён')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->hasRole('admin')),
                ]),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingPayments::route('/'),
            'create' => Pages\CreateBillingPayment::route('/create'),
            'view' => Pages\ViewBillingPayment::route('/{record}'),
            'edit' => Pages\EditBillingPayment::route('/{record}/edit'),
        ];
    }
}
