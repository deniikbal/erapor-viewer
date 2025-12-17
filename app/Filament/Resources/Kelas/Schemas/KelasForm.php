<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rombongan_belajar_id')
                    ->required(),
                TextInput::make('sekolah_id')
                    ->required(),
                TextInput::make('semester_id')
                    ->required(),
                TextInput::make('jurusan_id'),
                TextInput::make('ptk_id'),
                TextInput::make('nm_kelas'),
                TextInput::make('tingkat_pendidikan_id')
                    ->numeric(),
                TextInput::make('jenis_rombel')
                    ->numeric(),
                TextInput::make('nama_jurusan_sp'),
                TextInput::make('jurusan_sp_id'),
                TextInput::make('kurikulum_id')
                    ->required()
                    ->numeric(),
                TextInput::make('program'),
                TextInput::make('konsentrasi'),
            ]);
    }
}
