<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingInvoiceResource;
use App\Filament\Resources\BillingItemResource\Pages;
use App\Models\BillingItem;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\ServiceAddon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BillingItemResource extends Resource
{
    protected static ?string $model = BillingItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Начисления (Ledger)';

    protected static ?string $modelLabel = 'начисление';

    protected static ?string $pluralModelLabel = 'Начисления';

    protected static ?string $navigationGroup = 'Финансы';

    protected static ?int $navigationSort = 1;

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
                            ->required(),

                        Forms\Components\Select::make('scope')
                            ->label('Категория')
                            ->options(ServiceAddon::getScopeOptions())
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('period')
                            ->label('Период')
                            ->placeholder('YYYY-MM')
                            ->maxLength(7)
                            ->default(fn () => now()->format('Y-m')),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                BillingItem::STATUS_ACCRUED => 'Начислено',
                                BillingItem::STATUS_INVOICED => 'В счёте',
                                BillingItem::STATUS_VOID => 'Аннулировано',
                            ])
                            ->default(BillingItem::STATUS_ACCRUED)
                            ->disabled()
                            ->dehydrated(true),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Привязка')
                    ->schema([
                        Forms\Components\Select::make('addon_code')
                            ->label('Услуга из каталога')
                            ->options(
                                ServiceAddon::active()
                                    ->orderBy('scope')
                                    ->orderBy('title_ru')
                                    ->pluck('title_ru', 'code')
                            )
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $addon = ServiceAddon::where('code', $state)->first();
                                    if ($addon) {
                                        $set('scope', $addon->scope);
                                        $set('title_ru', $addon->title_ru);
                                        $set('title_uz', $addon->title_uz);
                                        if ($addon->pricing_type === 'fixed') {
                                            $set('unit_price', (int) $addon->value);
                                        }
                                    }
                                }
                            }),

                        Forms\Components\Select::make('source_type')
                            ->label('Тип источника')
                            ->options([
                                BillingItem::SOURCE_INBOUND => 'Приёмка',
                                BillingItem::SOURCE_SHIPMENT => 'Отправка',
                                BillingItem::SOURCE_RETURN => 'Возврат',
                                BillingItem::SOURCE_STORAGE_DAILY => 'Хранение (авто)',
                                BillingItem::SOURCE_MANUAL => 'Ручное',
                            ])
                            ->default(BillingItem::SOURCE_MANUAL)
                            ->native(false),

                        Forms\Components\TextInput::make('source_id')
                            ->label('ID источника')
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(3)
                    ->collapsed(),

                Forms\Components\Section::make('Детали начисления')
                    ->schema([
                        Forms\Components\TextInput::make('title_ru')
                            ->label('Название (RU)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title_uz')
                            ->label('Название (UZ)')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Сумма')
                    ->schema([
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Цена за единицу')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('сум')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Get $get) =>
                                $set('amount', (int) round($get('unit_price') * $get('qty')))),

                        Forms\Components\TextInput::make('qty')
                            ->label('Количество')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->default(1)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Get $get) =>
                                $set('amount', (int) round($get('unit_price') * $get('qty')))),

                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма')
                            ->required()
                            ->numeric()
                            ->suffix('сум')
                            ->disabled()
                            ->dehydrated(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->label('Комментарий')
                            ->rows(2),

                        Forms\Components\DateTimePicker::make('occurred_at')
                            ->label('Дата события')
                            ->default(now())
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentPeriod = now()->format('Y-m');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Период')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state) => $state === $currentPeriod ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        BillingItem::STATUS_ACCRUED => 'Начислено',
                        BillingItem::STATUS_INVOICED => 'В счёте',
                        BillingItem::STATUS_VOID => 'Аннулировано',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        BillingItem::STATUS_ACCRUED => 'warning',
                        BillingItem::STATUS_INVOICED => 'success',
                        BillingItem::STATUS_VOID => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('scope')
                    ->label('Категория')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ServiceAddon::getScopeOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'inbound' => 'info',
                        'pickpack' => 'success',
                        'storage' => 'warning',
                        'shipping' => 'primary',
                        'returns' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title_ru')
                    ->label('Название')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Цена')
                    ->money('UZS', locale: 'ru')
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Кол-во')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('UZS', locale: 'ru')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('UZS', locale: 'ru')),

                Tables\Columns\TextColumn::make('source_type')
                    ->label('Источник')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        BillingItem::SOURCE_INBOUND => 'Приёмка',
                        BillingItem::SOURCE_SHIPMENT => 'Отправка',
                        BillingItem::SOURCE_RETURN => 'Возврат',
                        BillingItem::SOURCE_STORAGE_DAILY => 'Хранение',
                        BillingItem::SOURCE_MANUAL => 'Ручное',
                        default => $state ?? '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Счёт')
                    ->url(fn (BillingItem $record): ?string =>
                        $record->invoice_id
                            ? BillingInvoiceResource::getUrl('view', ['record' => $record->invoice_id])
                            : null
                    )
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('addon_code')
                    ->label('Код услуги')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Дата события')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Создал')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Компания')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('period')
                    ->label('Период')
                    ->options(function () {
                        $periods = [];
                        for ($i = 0; $i < 12; $i++) {
                            $period = now()->subMonths($i)->format('Y-m');
                            $periods[$period] = now()->subMonths($i)->translatedFormat('F Y');
                        }
                        return $periods;
                    })
                    ->default(now()->format('Y-m')),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        BillingItem::STATUS_ACCRUED => 'Начислено',
                        BillingItem::STATUS_INVOICED => 'В счёте',
                        BillingItem::STATUS_VOID => 'Аннулировано',
                    ]),

                Tables\Filters\SelectFilter::make('scope')
                    ->label('Категория')
                    ->options(ServiceAddon::getScopeOptions()),

                Tables\Filters\SelectFilter::make('source_type')
                    ->label('Источник')
                    ->options([
                        BillingItem::SOURCE_INBOUND => 'Приёмка',
                        BillingItem::SOURCE_SHIPMENT => 'Отправка',
                        BillingItem::SOURCE_RETURN => 'Возврат',
                        BillingItem::SOURCE_STORAGE_DAILY => 'Хранение',
                        BillingItem::SOURCE_MANUAL => 'Ручное',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (BillingItem $record): bool =>
                        $record->status === BillingItem::STATUS_ACCRUED),
                Tables\Actions\Action::make('void')
                    ->label('Аннулировать')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Аннулировать начисление?')
                    ->modalDescription('Это действие нельзя отменить. Начисление будет исключено из счёта.')
                    ->visible(fn (BillingItem $record): bool =>
                        $record->status === BillingItem::STATUS_ACCRUED)
                    ->action(function (BillingItem $record): void {
                        $record->void();
                        Notification::make()
                            ->title('Начисление аннулировано')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('void_selected')
                        ->label('Аннулировать выбранные')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Аннулировать выбранные начисления?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $voided = 0;
                            foreach ($records as $record) {
                                if ($record->status === BillingItem::STATUS_ACCRUED) {
                                    $record->void();
                                    $voided++;
                                }
                            }
                            Notification::make()
                                ->title("Аннулировано: {$voided}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->hasRole('admin')),
                ]),
            ])
            ->defaultSort('occurred_at', 'desc');
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
            'index' => Pages\ListBillingItems::route('/'),
            'create' => Pages\CreateBillingItem::route('/create'),
            'view' => Pages\ViewBillingItem::route('/{record}'),
            'edit' => Pages\EditBillingItem::route('/{record}/edit'),
        ];
    }
}
