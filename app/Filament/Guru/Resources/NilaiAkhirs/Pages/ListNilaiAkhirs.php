<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs\Pages;

use App\Filament\Guru\Resources\NilaiAkhirs\NilaiAkhirResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNilaiAkhirs extends ListRecords
{
    protected static string $resource = NilaiAkhirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
