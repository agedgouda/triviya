<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;


class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name'),
                        TextInput::make('location')
                            ->label('Location'),
                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn ($record) => $record ? str($record->status)->title() : 'N/A'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'lg' => 3,
                    ]),
                View::make('filament.games.fields.players')
                    ->label('Games'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('host.name')
                    ->label('Host')
                    ->getStateUsing(fn($record) => "{$record->host()->first()->first_name} {$record->host()->first()->last_name}")
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('host', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                        });
                }),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => str($state)->title())
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter by Game Status')
                    ->options([
                        'new' => 'New',
                        'in progress' => 'In Progress',
                        'done' => 'Done',
                        'done-bonus' => 'Done-Bonus',
                    ]),
            ])
            ->searchPlaceholder('Search by Host Name')
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
