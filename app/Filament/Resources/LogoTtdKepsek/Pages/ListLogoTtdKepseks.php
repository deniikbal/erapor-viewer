<?php

namespace App\Filament\Resources\LogoTtdKepsek\Pages;

use App\Filament\Resources\LogoTtdKepsek\LogoTtdKepsekResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListLogoTtdKepseks extends ListRecords
{
    protected static string $resource = LogoTtdKepsekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
