<?php

namespace App\Filament\Resources\UserLogins\Pages;

use App\Filament\Resources\UserLogins\UserLoginResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserLogins extends ListRecords
{
    protected static string $resource = UserLoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
