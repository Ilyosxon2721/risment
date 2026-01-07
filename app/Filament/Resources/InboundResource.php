<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InboundResource\Pages;
use App\Models\Inbound;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InboundResource extends Resource
{
    protected static ?string $model = Inbound::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    
    protected static ?string $navigationGroup = 'Операции';
    protected static ?string $navigationLabel = 'Поставки';
    protected static ?string $modelLabel = 'Поставка';
    protected static ?string $pluralModelLabel = 'Поставки';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Номер поставки')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\DatePicker::make('planned_at')
                            ->label('Планируемая дата')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'draft' => 'Черновик',
                                'submitted' => 'Отправлена',
                                'in_transit' => 'В пути',
                                'receiving' => 'Идёт приёмка',
                                'completed' => 'Завершена',
                                'cancelled' => 'Отменена',
                            ])
                            ->required()
                            ->default('draft'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Информация об отправке')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Адрес отправки')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('executor_name')
                            ->label('Исполнитель')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('executor_phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Примечания')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания клиента')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes_receiving')
                            ->label('Примечания при приёмке')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn ($record) => !$record || !in_array($record->status, ['receiving', 'completed'])),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Номер')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('planned_at')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'submitted',
                        'info' => 'in_transit',
                        'warning' => 'receiving',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'submitted' => 'Отправлена',
                        'in_transit' => 'В пути',
                        'receiving' => 'Приёмка',
                        'completed' => 'Завершена',
                        'cancelled' => 'Отменена',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('has_discrepancies')
                    ->label('⚠️')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn ($state) => $state ? 'Есть расхождения' : 'Без расхождений'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Товаров')
                    ->counts('items')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'submitted' => 'Отправлена',
                        'in_transit' => 'В пути',
                        'receiving' => 'Приёмка',
                        'completed' => 'Завершена',
                        'cancelled' => 'Отменена',
                    ]),
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label('Компания')
                    ->searchable(),
                Tables\Filters\Filter::make('has_discrepancies')
                    ->label('С расхождениями')
                    ->query(fn (Builder $query): Builder => $query->where('has_discrepancies', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('receive')
                    ->label('Приёмка')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->url(fn (Inbound $record): string => InboundResource::getUrl('receive', ['record' => $record]))
                    ->visible(fn (Inbound $record): bool => in_array($record->status, ['submitted', 'in_transit', 'receiving'])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInbounds::route('/'),
            'create' => Pages\CreateInbound::route('/create'),
            'view' => Pages\ViewInbound::route('/{record}'),
            'edit' => Pages\EditInbound::route('/{record}/edit'),
            'receive' => Pages\ReceiveInbound::route('/{record}/receive'),
        ];
    }
}
