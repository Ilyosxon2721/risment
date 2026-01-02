<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationGroup = 'Операции';
    protected static ?string $navigationLabel = 'Тикеты';
    protected static ?string $modelLabel = 'Тикет';
    protected static ?string $pluralModelLabel = 'Тикеты';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->label('Компания')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Пользователь')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('subject')
                    ->label('Тема')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'open' => 'Открыт',
                        'pending' => 'Ожидает',
                        'closed' => 'Закрыт',
                    ])
                    ->required(),
                Forms\Components\Select::make('priority')
                    ->label('Приоритет')
                    ->options([
                        'low' => 'Низкий',
                        'medium' => 'Средний',
                        'high' => 'Высокий',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Тема')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'pending' => 'warning',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Открыт',
                        'pending' => 'Ожидает',
                        'closed' => 'Закрыт',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Приоритет')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high' => 'Высокий',
                        'medium' => 'Средний',
                        'low' => 'Низкий',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Сообщ.')
                    ->counts('messages')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'open' => 'Открыт',
                        'pending' => 'Ожидает',
                        'closed' => 'Закрыт',
                    ]),
                SelectFilter::make('priority')
                    ->label('Приоритет')
                    ->options([
                        'high' => 'Высокий',
                        'medium' => 'Средний',
                        'low' => 'Низкий',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Ответить')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Сообщение')
                            ->required()
                            ->rows(4),
                        Forms\Components\Select::make('new_status')
                            ->label('Изменить статус')
                            ->options([
                                'open' => 'Открыт',
                                'pending' => 'Ожидает ответа клиента',
                                'closed' => 'Закрыт',
                            ]),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        TicketMessage::create([
                            'ticket_id' => $record->id,
                            'user_id' => auth()->id(),
                            'message' => $data['message'],
                            'is_internal' => true,
                        ]);
                        
                        if (!empty($data['new_status'])) {
                            $record->update(['status' => $data['new_status']]);
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['open', 'pending'])->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'open')->count() > 0 ? 'danger' : 'warning';
    }
}
