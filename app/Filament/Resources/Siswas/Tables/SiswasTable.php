<?php

namespace App\Filament\Resources\Siswas\Tables;

use App\Filament\Resources\Siswas\Schemas\SiswaForm;
use App\Models\Kelas;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nm_siswa') // Sort by name alphabetically
            ->columns([
                TextColumn::make('nm_siswa')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->getStateUsing(function ($record) {
                        $kelasAktif = $record->kelasAktif();
                        if ($kelasAktif && $kelasAktif->kelas) {
                            $tingkatMap = [
                                '10' => 'X',
                                '11' => 'XI', 
                                '12' => 'XII'
                            ];
                            return $tingkatMap[$kelasAktif->kelas->tingkat_pendidikan_id] ?? $kelasAktif->kelas->tingkat_pendidikan_id;
                        }
                        return '-';
                    })
                    ->sortable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->getStateUsing(function ($record) {
                        $kelasAktif = $record->kelasAktif();
                        return $kelasAktif && $kelasAktif->kelas ? $kelasAktif->kelas->nm_kelas : '-';
                    })
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'Tingkat X',
                        '11' => 'Tingkat XI',
                        '12' => 'Tingkat XII',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->whereHas('anggotaKelas.kelas', function ($q) use ($data) {
                                $q->where('tingkat_pendidikan_id', $data['value'])
                                  ->where('jenis_rombel', 1);
                            });
                        }
                        return $query;
                    }),
                    
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(function () {
                        return Kelas::where('jenis_rombel', 1)
                            ->orderBy('tingkat_pendidikan_id')
                            ->orderBy('nm_kelas')
                            ->pluck('nm_kelas', 'rombongan_belajar_id')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->whereHas('anggotaKelas', function ($q) use ($data) {
                                $q->where('rombongan_belajar_id', $data['value']);
                            });
                        }
                        return $query;
                    }),
                    
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->modalHeading(fn ($record) => 'Edit Data Siswa: ' . $record->nm_siswa)
                    ->modalWidth('5xl')
                    ->form(fn ($form) => SiswaForm::configure($form))
                    ->fillForm(function ($record) {
                        $data = $record->toArray();
                        
                        // Add pelengkap data if exists
                        if ($record->pelengkap) {
                            $data['pelengkap'] = $record->pelengkap->toArray();
                        }
                        
                        return $data;
                    })
                    ->action(function ($record, $data) {
                        // Update main siswa data
                        $siswaData = collect($data)->except(['pelengkap'])->toArray();
                        $record->update($siswaData);
                        
                        // Update or create pelengkap data
                        if (isset($data['pelengkap'])) {
                            $pelengkapData = $data['pelengkap'];
                            $pelengkapData['peserta_didik_id'] = $record->peserta_didik_id;
                            
                            if ($record->pelengkap) {
                                $record->pelengkap->update($pelengkapData);
                            } else {
                                $pelengkapData['pelengkap_siswa_id'] = \Illuminate\Support\Str::uuid();
                                $record->pelengkap()->create($pelengkapData);
                            }
                        }
                    })
                    ->successNotificationTitle('Data siswa berhasil diperbarui'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
