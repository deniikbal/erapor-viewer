<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs\Tables;

use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\AnggotaKelas;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NilaiAkhirsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nm_siswa')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('anggotaKelas.kelas.nm_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('mapel.nm_mapel')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nilai_peng')
                    ->label('Nilai')
                    ->sortable()
                    ->alignCenter()
                    ->placeholder('-'),
                
                TextColumn::make('semester')
                    ->label('Semester')
                    ->formatStateUsing(fn ($state): string => $state ? "Semester {$state}" : '-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(function () {
                        return Mapel::pluck('nm_mapel', 'mata_pelajaran_id')->toArray();
                    })
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                    ]),
                
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(function () {
                        $user = Auth::user();
                        if ($user && isset($user->ptk)) {
                            return Kelas::where('ptk_id', $user->ptk->ptk_id)->pluck('nm_kelas', 'rombongan_belajar_id')->toArray();
                        }
                        return [];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('anggotaKelas', function (Builder $query) use ($value) {
                                $query->where('rombongan_belajar_id', $value);
                            })
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Eager load relasi untuk menghindari N+1 query
                $query->with(['siswa', 'mapel', 'anggotaKelas.kelas']);
                
                // Filter berdasarkan guru yang login (wali kelas)
                $user = Auth::user();
                if ($user && isset($user->ptk)) {
                    // Ambil kelas yang diampu oleh guru sebagai wali kelas
                    $kelasIds = Kelas::where('ptk_id', $user->ptk->ptk_id)->pluck('rombongan_belajar_id');
                    
                    if ($kelasIds->isNotEmpty()) {
                        // Ambil anggota kelas dari kelas yang diampu
                        $anggotaKelasIds = AnggotaKelas::whereIn('rombongan_belajar_id', $kelasIds)->pluck('anggota_rombel_id');
                        
                        if ($anggotaKelasIds->isNotEmpty()) {
                            $query->whereIn('anggota_rombel_id', $anggotaKelasIds);
                        } else {
                            // Jika tidak ada anggota kelas, return empty result
                            $query->whereRaw('1 = 0');
                        }
                    } else {
                        // Jika guru bukan wali kelas, return empty result
                        $query->whereRaw('1 = 0');
                    }
                }
                
                return $query;
            })
            ->defaultSort('siswa.nm_siswa', 'asc');
    }
}
