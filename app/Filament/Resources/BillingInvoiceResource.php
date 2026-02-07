<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingInvoiceResource\Pages;
use App\Models\BillingInvoice;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillingInvoiceResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Счета';

    protected static ?string $modelLabel = 'Счёт';

    protected static ?string $pluralModelLabel = 'Счета';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Основное')->schema([
                Forms\Components\Select::make('company_id')
                    ->label('Компания')
                    ->options(Company::pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('invoice_number')
                    ->label('Номер счёта')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('period')
                    ->label('Период (YYYY-MM)')
                    ->placeholder('2026-02'),

                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        BillingInvoice::STATUS_DRAFT => 'Черновик',
                        BillingInvoice::STATUS_ISSUED => 'Выставлен',
                        BillingInvoice::STATUS_PARTIALLY_PAID => 'Частично оплачен',
                        BillingInvoice::STATUS_PAID => 'Оплачен',
                        BillingInvoice::STATUS_CANCELLED => 'Отменён',
                    ])
                    ->required()
                    ->default(BillingInvoice::STATUS_DRAFT),
            ])->columns(2),

            Forms\Components\Section::make('Суммы')->schema([
                Forms\Components\TextInput::make('subtotal')
                    ->label('Подитог')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('discount')
                    ->label('Скидка')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('total')
                    ->label('Итого')
                    ->numeric()
                    ->default(0),
            ])->columns(3),

            Forms\Components\Section::make('Даты')->schema([
                Forms\Components\DatePicker::make('issue_date')
                    ->label('Дата выставления'),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Срок оплаты'),

                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Дата оплаты'),
            ])->columns(3),

            Forms\Components\Textarea::make('notes')
                ->label('Заметки')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Номер')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Период')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '', ' ') . ' UZS')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => fn ($state) => in_array($state, ['draft', 'partially_paid']),
                        'success' => 'paid',
                        'info' => 'issued',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'overdue']),
                    ])
                    ->formatStateUsing(fn (BillingInvoice $record) => $record->getStatusLabel()),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Выставлен')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Срок')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        BillingInvoice::STATUS_DRAFT => 'Черновик',
                        BillingInvoice::STATUS_ISSUED => 'Выставлен',
                        BillingInvoice::STATUS_PARTIALLY_PAID => 'Частично оплачен',
                        BillingInvoice::STATUS_PAID => 'Оплачен',
                        BillingInvoice::STATUS_CANCELLED => 'Отменён',
                    ]),

                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Компания')
                    ->options(Company::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingInvoices::route('/'),
            'create' => Pages\CreateBillingInvoice::route('/create'),
            'view' => Pages\ViewBillingInvoice::route('/{record}'),
            'edit' => Pages\EditBillingInvoice::route('/{record}/edit'),
        ];
    }
}
