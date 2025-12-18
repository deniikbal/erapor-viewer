<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs\Pages;

use App\Filament\Guru\Resources\NilaiAkhirs\NilaiAkhirResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateNilaiAkhir extends CreateRecord
{
    protected static string $resource = NilaiAkhirResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate UUID untuk primary key
        $data['id_nilai_akhir'] = (string) Str::uuid();
        
        return $data;
    }
}
