<?php

namespace App\Filament\Resources\SubscriptionPlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LimitsRelationManager extends RelationManager
{
    protected static string $relationship = 'limits';

    protected static ?string $title = 'Включенные лимиты';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('included_shipments')
                    ->label('Отправок включено')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('шт/мес'),
                
                Forms\Components\TextInput::make('included_boxes')
                    ->label('Коробов на хранении')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('шт'),
                
                Forms\Components\TextInput::make('included_bags')
                    ->label('Мешков на хранении')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('шт'),
                
                Forms\Components\TextInput::make('included_inbound_boxes')
                    ->label('Приёмка коробов')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('шт/мес'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('included_shipments')
                    ->label('Отправки')
                    ->suffix(' шт/мес')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('included_boxes')
                    ->label('Коробы')
                    ->suffix(' шт')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('included_bags')
                    ->label('Мешки')
                    ->suffix(' шт')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('included_inbound_boxes')
                    ->label('Приёмка')
                    ->suffix(' шт/мес')
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => !$this->ownerRecord->limits()->exists()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
