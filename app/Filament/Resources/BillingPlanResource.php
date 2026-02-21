<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingPlanResource\Pages;
use App\Models\BillingPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BillingPlanResource extends Resource
{
    protected static ?string $model = BillingPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Тарифные планы';

    protected static ?string $modelLabel = 'тарифный план';

    protected static ?string $pluralModelLabel = 'Тарифные планы';

    protected static ?string $navigationGroup = 'Финансы';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Основное')
                            ->schema([
                                Forms\Components\Section::make('Идентификация')
                                    ->schema([
                                        Forms\Components\TextInput::make('code')
                                            ->label('Код')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50)
                                            ->alphaDash(),

                                        Forms\Components\TextInput::make('name_ru')
                                            ->label('Название (RU)')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('name_uz')
                                            ->label('Название (UZ)')
                                            ->maxLength(255),

                                        Forms\Components\Select::make('billing_model')
                                            ->label('Модель биллинга')
                                            ->options([
                                                'subscription' => 'Подписка',
                                                'monthly' => 'Помесячно',
                                                'payg' => 'По факту (PAYG)',
                                            ])
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Описание')
                                    ->schema([
                                        Forms\Components\Textarea::make('description_ru')
                                            ->label('Описание (RU)')
                                            ->rows(3),

                                        Forms\Components\Textarea::make('description_uz')
                                            ->label('Описание (UZ)')
                                            ->rows(3),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Преимущества')
                                    ->schema([
                                        Forms\Components\TagsInput::make('features_ru')
                                            ->label('Преимущества (RU)')
                                            ->placeholder('Добавить преимущество')
                                            ->splitKeys(['Tab', 'Enter']),

                                        Forms\Components\TagsInput::make('features_uz')
                                            ->label('Преимущества (UZ)')
                                            ->placeholder('Добавить преимущество')
                                            ->splitKeys(['Tab', 'Enter']),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Цены')
                            ->schema([
                                Forms\Components\Section::make('Стоимость')
                                    ->schema([
                                        Forms\Components\TextInput::make('monthly_fee')
                                            ->label('Абонентская плата')
                                            ->required()
                                            ->numeric()
                                            ->suffix('сум/месяц')
                                            ->default(0),

                                        Forms\Components\TextInput::make('discount_percent')
                                            ->label('Скидка на услуги')
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Тарифы на услуги')
                                    ->schema([
                                        Forms\Components\TextInput::make('storage_rate')
                                            ->label('Хранение')
                                            ->required()
                                            ->numeric()
                                            ->suffix('сум/коробко-день'),

                                        Forms\Components\TextInput::make('shipment_rate')
                                            ->label('Отправка')
                                            ->required()
                                            ->numeric()
                                            ->suffix('сум/заказ'),

                                        Forms\Components\TextInput::make('receiving_rate')
                                            ->label('Приёмка')
                                            ->required()
                                            ->numeric()
                                            ->suffix('сум/место'),

                                        Forms\Components\TextInput::make('return_rate')
                                            ->label('Возврат')
                                            ->required()
                                            ->numeric()
                                            ->suffix('сум/шт')
                                            ->default(0),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Включено в тариф')
                                    ->schema([
                                        Forms\Components\TextInput::make('included_storage_units')
                                            ->label('Бесплатное хранение')
                                            ->numeric()
                                            ->suffix('коробко-дней')
                                            ->default(0),

                                        Forms\Components\TextInput::make('included_shipments')
                                            ->label('Бесплатные отправки')
                                            ->numeric()
                                            ->suffix('шт')
                                            ->default(0),

                                        Forms\Components\TextInput::make('included_receiving_units')
                                            ->label('Бесплатная приёмка')
                                            ->numeric()
                                            ->suffix('мест')
                                            ->default(0),

                                        Forms\Components\Toggle::make('returns_included')
                                            ->label('Возвраты включены')
                                            ->helperText('Обработка возвратов бесплатно'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Лимиты')
                            ->schema([
                                Forms\Components\Section::make('Лимиты заказов')
                                    ->schema([
                                        Forms\Components\TextInput::make('min_orders_month')
                                            ->label('Мин. заказов/месяц')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Для рекомендации клиентам'),

                                        Forms\Components\TextInput::make('max_orders_month')
                                            ->label('Макс. заказов/месяц')
                                            ->numeric()
                                            ->nullable()
                                            ->helperText('Пусто = без лимита'),

                                        Forms\Components\TextInput::make('max_storage_units')
                                            ->label('Макс. хранение')
                                            ->numeric()
                                            ->nullable()
                                            ->suffix('ед.')
                                            ->helperText('Пусто = без лимита'),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Бесплатные дни')
                                    ->schema([
                                        Forms\Components\TextInput::make('free_storage_days')
                                            ->label('Бесплатное хранение новых товаров')
                                            ->numeric()
                                            ->suffix('дней')
                                            ->default(0),

                                        Forms\Components\TextInput::make('free_return_days')
                                            ->label('Бесплатное хранение возвратов')
                                            ->numeric()
                                            ->suffix('дней')
                                            ->default(10),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Отображение')
                            ->schema([
                                Forms\Components\Section::make('Настройки отображения')
                                    ->schema([
                                        Forms\Components\TextInput::make('badge')
                                            ->label('Бейдж')
                                            ->maxLength(50)
                                            ->placeholder('Популярный, VIP, Новинка'),

                                        Forms\Components\TextInput::make('sort')
                                            ->label('Сортировка')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\Toggle::make('is_popular')
                                            ->label('Популярный тариф')
                                            ->helperText('Выделить на странице тарифов'),

                                        Forms\Components\Toggle::make('is_visible')
                                            ->label('Показывать клиентам')
                                            ->default(true),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Активен')
                                            ->default(true)
                                            ->helperText('Можно подписаться'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name_ru')
                    ->label('Название')
                    ->searchable()
                    ->description(fn (BillingPlan $record): ?string => $record->badge),

                Tables\Columns\TextColumn::make('monthly_fee')
                    ->label('Абонплата')
                    ->formatStateUsing(fn ($state) => $state > 0
                        ? number_format($state, 0, '', ' ') . ' сум'
                        : 'Бесплатно')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Скидка')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}%" : '-')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('billing_model')
                    ->label('Модель')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'subscription' => 'Подписка',
                        'monthly' => 'Помесячно',
                        'payg' => 'PAYG',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'subscription' => 'primary',
                        'payg' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('activeSubscriptions_count')
                    ->label('Подписок')
                    ->counts('activeSubscriptions')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Попул.')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Видим.')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Актив.')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('billing_model')
                    ->label('Модель')
                    ->options([
                        'subscription' => 'Подписка',
                        'monthly' => 'Помесячно',
                        'payg' => 'PAYG',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные'),

                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Видимые'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Копировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (BillingPlan $record): void {
                        $new = $record->replicate();
                        $new->code = $record->code . '_copy';
                        $new->name_ru = $record->name_ru . ' (копия)';
                        $new->is_active = false;
                        $new->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort')
            ->defaultSort('sort');
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
            'index' => Pages\ListBillingPlans::route('/'),
            'create' => Pages\CreateBillingPlan::route('/create'),
            'edit' => Pages\EditBillingPlan::route('/{record}/edit'),
        ];
    }
}
