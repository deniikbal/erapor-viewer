<?php

namespace App\Filament\Resources\Kelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tingkat_pendidikan_id')
            ->columns([
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->sortable(),
                TextColumn::make('nm_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('waliKelas.nama')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada wali kelas'),
                TextColumn::make('anggota_kelas_count')
                    ->label('Jumlah Siswa')
                    ->sortable(),
                TextColumn::make('semester_id')
                    ->label('Semester')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view_anggota')
                    ->label('Cek Anggota')
                    ->icon('heroicon-o-users')
                    ->modalHeading(fn ($record) => 'Anggota Kelas: ' . $record->nm_kelas)
                    ->modalContent(function ($record) {
                        $siswaList = $record->anggotaKelas()
                            ->with('siswa')
                            ->get()
                            ->map(function ($anggota) {
                                return [
                                    'nama' => $anggota->siswa->nm_siswa ?? 'N/A',
                                    'nis' => $anggota->siswa->nis ?? 'N/A',
                                    'nisn' => $anggota->siswa->nisn ?? 'N/A',
                                    'jenis_kelamin' => $anggota->siswa->jenis_kelamin ?? 'N/A',
                                ];
                            })
                            ->sortBy('nama')
                            ->values(); // Reset array keys to 0, 1, 2, etc.

                        return view('filament.components.siswa-list', [
                            'siswa' => $siswaList,
                            'total' => $siswaList->count()
                        ]);
                    })
                    ->modalWidth('4xl')
                    ->action(function () {
                        // No action needed, just show modal
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
