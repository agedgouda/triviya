<?php

namespace App\Filament\Resources\GameProductResource\Pages;

use App\Filament\Resources\GameProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameProduct extends EditRecord
{
    protected static string $resource = GameProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
