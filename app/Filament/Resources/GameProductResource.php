<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameProductResource\Pages;
use App\Models\GameProduct;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameProductResource extends Resource
{
    protected static ?string $model = GameProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name'),
                TextInput::make('game_count')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('Number of Games'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->label('Price'),
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('game_count')
                    ->label('Games'),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('usd'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListGameProducts::route('/'),
            'create' => Pages\CreateGameProduct::route('/create'),
            'edit' => Pages\EditGameProduct::route('/{record}/edit'),
        ];
    }
}
