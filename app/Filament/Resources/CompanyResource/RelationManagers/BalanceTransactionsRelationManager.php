<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Models\BillingBalance;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class BalanceTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'billingTransactions';

    protected static ?string $title = 'Баланс';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(function () {
                $balance = BillingBalance::getOrCreate($this->getOwnerRecord()->id);
                $bal = (float) $balance->balance;
                $color = $bal < 0 ? '🔴' : '🟢';
                $sign  = $bal < 0 ? '−' : '+';
                return "Баланс {$color}: {$sign}" . number_format(abs($bal), 0, '', ' ') . ' UZS';
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'topup' ? 'success' : 'danger')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'topup'  => 'Пополнение',
                        'charge' => 'Списание',
                        default  => $state,
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->formatStateUsing(fn ($state): string =>
                        ($state >= 0 ? '+' : '−') . number_format(abs($state), 0, '', ' ') . ' UZS'
                    )
                    ->color(fn ($state): string => $state >= 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('balance_after')
                    ->label('Баланс после')
                    ->formatStateUsing(fn ($state): string =>
                        ($state >= 0 ? '' : '−') . number_format(abs($state), 0, '', ' ') . ' UZS'
                    )
                    ->color(fn ($state): string => $state >= 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('topup')
                    ->label('Пополнить баланс')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма пополнения (UZS)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('Описание / комментарий')
                            ->default('Пополнение баланса')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $balance = BillingBalance::getOrCreate($this->getOwnerRecord()->id);
                        $balance->topup(
                            (float) $data['amount'],
                            $data['description']
                        );

                        Notification::make()
                            ->title('Баланс пополнен')
                            ->body(number_format($data['amount'], 0, '', ' ') . ' UZS зачислено')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('manual_charge')
                    ->label('Ручное списание')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма списания (UZS)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('Причина')
                            ->default('Ручное списание')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $balance = BillingBalance::getOrCreate($this->getOwnerRecord()->id);
                        $balance->charge(
                            (float) $data['amount'],
                            $data['description']
                        );

                        Notification::make()
                            ->title('Сумма списана')
                            ->body(number_format($data['amount'], 0, '', ' ') . ' UZS списано')
                            ->warning()
                            ->send();
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
