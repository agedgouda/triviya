<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;


use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Checkbox::make('is_admin')
                            ->label('Admin')
                            ->inline(),
                    ])
                    ->columns(1)
                    ->extraAttributes(['class' => 'justify-end flex']),

                Fieldset::make('Name')
                    ->schema([
                        TextInput::make('first_name')->required()->maxLength(255),
                        TextInput::make('last_name')->required()->maxLength(255),
                    ])
                    ->columns(2),

                Fieldset::make('Contact Information')
                    ->schema([
                        TextInput::make('email')->required()->maxLength(255),
                        TextInput::make('phone_number')->maxLength(255),
                        DatePicker::make('birthday')->required()->native(false),
                    ])
                    ->columns(3),

                View::make('filament.users.fields.games')
                    ->label('Games'),
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
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
