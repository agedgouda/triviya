<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Checkbox::make('is_admin')
                            ->label('Admin')
                            ->inline(),
                    ])
                    ->columns(1)
                    ->extraAttributes(['class' => 'justify-end flex'])
                    ->columnSpanFull(),

                Fieldset::make('Name')
                    ->schema([
                        TextInput::make('first_name')->required()->maxLength(255),
                        TextInput::make('last_name')->required()->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Fieldset::make('Contact Information')
                    ->schema([
                        TextInput::make('email')->required()->maxLength(255),
                        TextInput::make('phone_number')->tel()->mask('999-999-9999')->maxLength(255),
                        TextInput::make('games_available')->numeric()->minValue(0)->label('Games Available'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        View::make('filament.users.fields.games'),
                        View::make('filament.users.fields.game-product-purchases'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CheckboxColumn::make('is_admin')
                    ->label('admin'),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->first_name.' '.$record->last_name)
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->where(function ($subQuery) use ($search) {
                            $subQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('games_available')
                    ->label('Games Available')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
