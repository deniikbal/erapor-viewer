<?php

namespace App\Filament\Resources\Ptks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PtkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ptk_id')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('nip'),
                TextInput::make('jenis_ptk_id')
                    ->required()
                    ->numeric(),
                TextInput::make('jenis_kelamin')
                    ->required(),
                TextInput::make('tempat_lahir')
                    ->required(),
                DatePicker::make('tanggal_lahir')
                    ->required(),
                TextInput::make('nuptk'),
                TextInput::make('alamat_jalan'),
                TextInput::make('status_keaktifan_id')
                    ->required()
                    ->numeric(),
                TextInput::make('soft_delete')
                    ->required()
                    ->numeric(),
            ]);
    }
}
