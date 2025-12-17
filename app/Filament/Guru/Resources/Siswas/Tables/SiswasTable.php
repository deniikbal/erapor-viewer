<?php

namespace App\Filament\Guru\Resources\Siswas\Tables;

use App\Filament\Guru\Resources\Siswas\Schemas\SiswaForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->getStateUsing(function ($record) {
                        $kelasAktif = $record->kelasAktif();
                        return $kelasAktif && $kelasAktif->kelas ? $kelasAktif->kelas->nm_kelas : '-';
                    })
                    ->searchable(),
                TextColumn::make('telepon_siswa')
                    ->label('No. HP')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit Data')
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
                    
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn ($record) => route('guru.siswa.pdf', $record->peserta_didik_id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                Action::make('pdf_all')
                    ->label('PDF Semua Siswa')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(route('guru.siswa.pdf.all'))
                    ->openUrlInNewTab(),
            ])
            ->description(function () {
                $user = Auth::user();
                $kelasWali = \App\Models\Kelas::where('ptk_id', $user->ptk_id)
                    ->where('jenis_rombel', 1)
                    ->first();
                
                if ($kelasWali) {
                    return "Menampilkan siswa dari kelas: {$kelasWali->nm_kelas}. Anda dapat mengedit data siswa dengan mengklik tombol 'Edit Data'.";
                }
                
                return "Anda belum ditugaskan sebagai wali kelas";
            });
    }
}
