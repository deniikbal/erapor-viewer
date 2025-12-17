<?php

namespace App\Filament\Resources\UserLogins\Pages;

use App\Filament\Resources\UserLogins\UserLoginResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserLogin extends EditRecord
{
    protected static string $resource = UserLoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
