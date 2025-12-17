<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Siswa')
                    ->description('Informasi dasar siswa')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nm_siswa')
                            ->label('Nama Lengkap')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('nis')
                            ->label('NIS')
                            ->required(),
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required(),
                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir'),
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->displayFormat('d/m/Y'),
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        TextInput::make('agama')
                            ->label('Agama'),
                        
                        Textarea::make('alamat_siswa')
                            ->label('Alamat Siswa')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('telepon_siswa')
                            ->label('No. HP Siswa')
                            ->tel(),
                        DatePicker::make('diterima_tanggal')
                            ->label('Tanggal Diterima')
                            ->displayFormat('d/m/Y'),
                        // Data Orang Tua
                        TextInput::make('nm_ayah')
                            ->label('Nama Ayah'),
                        TextInput::make('nm_ibu')
                            ->label('Nama Ibu'),
                        TextInput::make('pekerjaan_ayah')
                            ->label('Pekerjaan Ayah'),
                        TextInput::make('pekerjaan_ibu')
                            ->label('Pekerjaan Ibu'),
                        TextInput::make('nm_wali')
                            ->label('Nama Wali'),
                        TextInput::make('pekerjaan_wali')
                            ->label('Pekerjaan Wali'),
                    ]),

                Section::make('Data Pelengkap')
                    ->description('Informasi tambahan siswa')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->relationship('pelengkap')
                    ->schema([
                        Select::make('status_dalam_kel')
                            ->label('Status dalam Keluarga')
                            ->options([
                                'Anak Kandung' => 'Anak Kandung',
                                'Anak Angkat' => 'Anak Angkat',
                                'Anak Tiri' => 'Anak Tiri',
                            ])
                            ->placeholder('Pilih status dalam keluarga'),
                        TextInput::make('anak_ke')
                            ->label('Anak Ke-')
                            ->numeric(),
                        TextInput::make('sekolah_asal')
                            ->label('Sekolah Asal')
                            ->columnSpanFull(),
                        TextInput::make('diterima_kelas')
                            ->label('Diterima di Kelas'),
                        Textarea::make('alamat_ortu')
                            ->label('Alamat Orang Tua')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('telepon_ortu')
                            ->label('Telepon Orang Tua')
                            ->tel(),
                    ]),
            ]);
    }
}
