<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $title = 'Скидки';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Тип скидки')
                ->options([
                    'percent' => 'Процент (%)',
                    'fixed'   => 'Фиксированная сумма (UZS)',
                ])
                ->default('percent')
                ->required()
                ->live(),

            Forms\Components\TextInput::make('value')
                ->label(fn (Forms\Get $get) => $get('type') === 'percent' ? 'Размер скидки (%)' : 'Сумма скидки (UZS)')
                ->numeric()
                ->minValue(0)
                ->maxValue(fn (Forms\Get $get) => $get('type') === 'percent' ? 100 : null)
                ->required(),

            Forms\Components\Select::make('target')
                ->label('Применяется к')
                ->options([
                    'all'          => 'Всё (подписка + сверхлимит)',
                    'subscription' => 'Только подписка',
                    'overage'      => 'Только сверхлимитные услуги',
                ])
                ->default('all')
                ->required(),

            Forms\Components\TextInput::make('reason')
                ->label('Причина / примечание')
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Действует с')
                ->nullable(),

            Forms\Components\DateTimePicker::make('ends_at')
                ->label('Действует до')
                ->nullable()
                ->after('starts_at'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('target')
                    ->label('Применяется к')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all'          => 'success',
                        'subscription' => 'info',
                        'overage'      => 'warning',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all'          => 'Всё',
                        'subscription' => 'Подписка',
                        'overage'      => 'Сверхлимит',
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->label('Скидка')
                    ->formatStateUsing(fn ($state, $record): string =>
                        $record->type === 'percent'
                            ? "{$state}%"
                            : number_format($state, 0, '', ' ') . ' UZS'
                    ),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Примечание')
                    ->limit(40)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('С')
                    ->dateTime('d.m.Y')
                    ->placeholder('Без ограничений'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('До')
                    ->dateTime('d.m.Y')
                    ->placeholder('Бессрочно'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->state(fn ($record): bool => $record->isActive())
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить скидку')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),
                Tables\Actions\DeleteAction::make()->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
