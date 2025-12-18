<?php

namespace App\Filament\Resources\LogoTtdKepsek\Pages;

use App\Filament\Resources\LogoTtdKepsek\LogoTtdKepsekResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLogoTtdKepsek extends CreateRecord
{
    protected static string $resource = LogoTtdKepsekResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure sekolah_id is set if not provided (though form has readonly default)
        // If user is logged in and has sekolah_id, usage might handle it.
        // For now, we trust the form data or model events.
        return $data;
    }
}
