<?php

namespace App\Filament\Resources\Ptks\Pages;

use App\Filament\Resources\Ptks\PtkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPtk extends EditRecord
{
    protected static string $resource = PtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
