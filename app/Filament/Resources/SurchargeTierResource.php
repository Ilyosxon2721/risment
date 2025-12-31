<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurchargeTierResource\Pages;
use App\Models\SurchargeTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SurchargeTierResource extends Resource
{
    protected static ?string $model = SurchargeTier::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    
    protected static ?string $navigationLabel = 'Надбавки';
    
    protected static ?string $modelLabel = 'надбавка';
    
    protected static ?string $pluralModelLabel = 'Надбавки разового тарифа';
    
    protected static ?string $navigationGroup = 'Ценообразование';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Диапазон отправок')
                    ->description('Укажите диапазон количества отправок для этой надбавки')
                    ->schema([
                        Forms\Components\TextInput::make('min_shipments')
                            ->label('Минимум отправок')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->integer(),
                        
                        Forms\Components\TextInput::make('max_shipments')
                            ->label('Максимум отправок')
                            ->numeric()
                            ->minValue(1)
                            ->integer()
                            ->helperText('Оставьте пустым для "без ограничений"'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Надбавка и настройки')
                    ->schema([
                        Forms\Components\TextInput::make('surcharge_percent')
                            ->label('Процент надбавки')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Например: 10 для 10% надбавки'),
                        
                        Forms\Components\TextInput::make('sort')
                            ->label('Порядок сортировки')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->integer(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('#')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('min_shipments')
                    ->label('От')
                    ->numeric()
                    ->sortable()
                    ->suffix(' шт'),
                
                Tables\Columns\TextColumn::make('max_shipments')
                    ->label('До')
                    ->numeric()
                    ->sortable()
                    ->default('∞')
                    ->suffix(fn ($record) => $record->max_shipments ? ' шт' : ''),
                
                Tables\Columns\TextColumn::make('surcharge_percent')
                    ->label('Надбавка')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state <= 10 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),
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
            'index' => Pages\ListSurchargeTiers::route('/'),
            'create' => Pages\CreateSurchargeTier::route('/create'),
            'edit' => Pages\EditSurchargeTier::route('/{record}/edit'),
        ];
    }
}
