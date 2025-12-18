<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs\Schemas;

use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\AnggotaKelas;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class NilaiAkhirForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('peserta_didik_id')
                    ->label('Siswa')
                    ->options(function () {
                        // Hanya tampilkan siswa dari kelas yang diampu wali kelas
                        $user = Auth::user();
                        if ($user && isset($user->ptk)) {
                            $kelasIds = Kelas::where('ptk_id', $user->ptk->ptk_id)->pluck('rombongan_belajar_id');
                            if ($kelasIds->isNotEmpty()) {
                                $siswaIds = AnggotaKelas::whereIn('rombongan_belajar_id', $kelasIds)->pluck('peserta_didik_id');
                                return Siswa::whereIn('peserta_didik_id', $siswaIds)->pluck('nm_siswa', 'peserta_didik_id');
                            }
                        }
                        return [];
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Auto-set anggota_rombel_id berdasarkan siswa yang dipilih
                        if ($state) {
                            $user = Auth::user();
                            if ($user && isset($user->ptk)) {
                                $kelasIds = Kelas::where('ptk_id', $user->ptk->ptk_id)->pluck('rombongan_belajar_id');
                                $anggotaRombelId = AnggotaKelas::where('peserta_didik_id', $state)
                                    ->whereIn('rombongan_belajar_id', $kelasIds)
                                    ->first()?->anggota_rombel_id;
                                $set('anggota_rombel_id', $anggotaRombelId);
                            }
                        }
                    }),
                
                Hidden::make('anggota_rombel_id'),
                
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mapel', 'nm_mapel')
                    ->searchable()
                    ->required(),
                
                TextInput::make('nilai_peng')
                    ->label('Nilai Pengetahuan')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                
                TextInput::make('predikat_peng')
                    ->label('Predikat Pengetahuan')
                    ->maxLength(2),
                
                TextInput::make('nilai_ket')
                    ->label('Nilai Keterampilan')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                
                TextInput::make('predikat_ket')
                    ->label('Predikat Keterampilan')
                    ->maxLength(2),
                
                Select::make('semester')
                    ->label('Semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                    ])
                    ->required(),
            ]);
    }
}
