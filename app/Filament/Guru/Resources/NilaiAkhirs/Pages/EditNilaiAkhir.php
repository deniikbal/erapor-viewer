<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs\Pages;

use App\Filament\Guru\Resources\NilaiAkhirs\NilaiAkhirResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNilaiAkhir extends EditRecord
{
    protected static string $resource = NilaiAkhirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
