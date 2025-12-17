<?php

namespace App\Filament\Guru\Resources\Siswas\Pages;

use App\Filament\Guru\Resources\Siswas\SiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
