<?php

namespace App\Filament\Resources\UserLogins\Pages;

use App\Filament\Resources\UserLogins\UserLoginResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserLogin extends CreateRecord
{
    protected static string $resource = UserLoginResource::class;
}
