<?php

namespace App\Filament\Resources\Ptks\Pages;

use App\Filament\Resources\Ptks\PtkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPtks extends ListRecords
{
    protected static string $resource = PtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
