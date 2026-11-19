<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages\CreateGame;
use App\Filament\Resources\GameResource\Pages\EditGame;
use App\Filament\Resources\GameResource\Pages\ListGames;
use App\Models\Game;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name'),
                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn ($record) => $record ? str($record->status)->title() : 'N/A'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'lg' => 2,
                    ])
                    ->columnSpanFull(),
                View::make('filament.games.fields.players')
                    ->columnSpanFull(),
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
                    ->getStateUsing(fn ($record) => "{$record->host()->first()->first_name} {$record->host()->first()->last_name}")
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListGames::route('/'),
            'create' => CreateGame::route('/create'),
            'edit' => EditGame::route('/{record}/edit'),
        ];
    }
}
