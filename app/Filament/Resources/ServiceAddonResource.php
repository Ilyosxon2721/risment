<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceAddonResource\Pages;
use App\Models\ServiceAddon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceAddonResource extends Resource
{
    protected static ?string $model = ServiceAddon::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Доп. услуги';

    protected static ?string $modelLabel = 'услуга';

    protected static ?string $pluralModelLabel = 'Дополнительные услуги';

    protected static ?string $navigationGroup = 'Тарифы';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText('Код нельзя изменить после создания')
                            ->placeholder('ADDON_EXAMPLE'),

                        Forms\Components\Select::make('scope')
                            ->label('Категория')
                            ->options(ServiceAddon::getScopeOptions())
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('pricing_type')
                            ->label('Тип цены')
                            ->options(ServiceAddon::getPricingTypeOptions())
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Ценообразование')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label(fn (Get $get) => match ($get('pricing_type')) {
                                'percent' => 'Процент',
                                default => 'Цена (UZS)',
                            })
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn (Get $get) => match ($get('pricing_type')) {
                                'percent' => '%',
                                default => 'сум',
                            })
                            ->visible(fn (Get $get) => in_array($get('pricing_type'), ['fixed', 'percent']))
                            ->required(fn (Get $get) => in_array($get('pricing_type'), ['fixed', 'percent'])),

                        Forms\Components\Fieldset::make('Цены по категориям')
                            ->schema([
                                Forms\Components\TextInput::make('meta.MICRO')
                                    ->label('MICRO (≤30 см)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('сум'),
                                Forms\Components\TextInput::make('meta.MGT')
                                    ->label('MGT (31-60 см)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('сум'),
                                Forms\Components\TextInput::make('meta.SGT')
                                    ->label('SGT (61-120 см)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('сум'),
                                Forms\Components\TextInput::make('meta.KGT')
                                    ->label('KGT (>120 см)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('сум'),
                            ])
                            ->columns(4)
                            ->visible(fn (Get $get) => $get('pricing_type') === 'by_category'),

                        Forms\Components\Placeholder::make('manual_hint')
                            ->label('')
                            ->content('Цена вводится вручную при создании начисления')
                            ->visible(fn (Get $get) => $get('pricing_type') === 'manual'),
                    ]),

                Forms\Components\Section::make('Названия')
                    ->schema([
                        Forms\Components\TextInput::make('title_ru')
                            ->label('Название (RU)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Фотоотчёт при приёмке'),

                        Forms\Components\TextInput::make('title_uz')
                            ->label('Название (UZ)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Qabul qilishda foto hisobot'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Единицы измерения')
                    ->schema([
                        Forms\Components\TextInput::make('unit_ru')
                            ->label('Единица (RU)')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('за фото'),

                        Forms\Components\TextInput::make('unit_uz')
                            ->label('Единица (UZ)')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('har bir foto uchun'),
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
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Настройки')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),

                        Forms\Components\TextInput::make('sort')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->badge(),

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
                    ->limit(40),

                Tables\Columns\TextColumn::make('pricing_type')
                    ->label('Тип цены')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ServiceAddon::getPricingTypeOptions()[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Значение')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->pricing_type === 'percent') {
                            return number_format($state * 100, 0) . '%';
                        }
                        if ($record->pricing_type === 'fixed') {
                            return number_format($state, 0, '.', ' ') . ' сум';
                        }
                        if ($record->pricing_type === 'by_category') {
                            return 'По категории';
                        }
                        return 'Ручной';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_ru')
                    ->label('Единица')
                    ->limit(20)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Порядок')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scope')
                    ->label('Категория')
                    ->options(ServiceAddon::getScopeOptions()),

                Tables\Filters\SelectFilter::make('pricing_type')
                    ->label('Тип цены')
                    ->options(ServiceAddon::getPricingTypeOptions()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность')
                    ->placeholder('Все')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scope')
            ->reorderable('sort');
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
            'index' => Pages\ListServiceAddons::route('/'),
            'create' => Pages\CreateServiceAddon::route('/create'),
            'edit' => Pages\EditServiceAddon::route('/{record}/edit'),
        ];
    }
}
