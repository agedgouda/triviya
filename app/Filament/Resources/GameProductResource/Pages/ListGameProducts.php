<?php

namespace App\Filament\Resources\GameProductResource\Pages;

use App\Filament\Resources\GameProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGameProducts extends ListRecords
{
    protected static string $resource = GameProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
