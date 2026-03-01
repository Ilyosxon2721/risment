<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ManagedCompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'managedCompanies';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Управляемые компании';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Компания')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inn')
                    ->label('ИНН')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->label('Назначить компанию')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make()
                    ->label('Снять'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                ]),
            ]);
    }
}
