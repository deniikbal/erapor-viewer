<?php

namespace App\Filament\Guru\Resources\Siswas;

use App\Filament\Guru\Resources\Siswas\Pages\CreateSiswa;
use App\Filament\Guru\Resources\Siswas\Pages\EditSiswa;
use App\Filament\Guru\Resources\Siswas\Pages\ListSiswas;
use App\Filament\Guru\Resources\Siswas\Schemas\SiswaForm;
use App\Filament\Guru\Resources\Siswas\Tables\SiswasTable;
use App\Models\Siswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SiswaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        return parent::getEloquentQuery()
            ->with(['pelengkap', 'anggotaKelas.kelas'])
            ->whereHas('anggotaKelas.kelas', function ($query) use ($user) {
                $query->where('ptk_id', $user->ptk_id)
                      ->where('jenis_rombel', 1);
            })
            ->orderBy('nm_siswa');
    }

    public static function getNavigationLabel(): string
    {
        return 'Siswa Wali Kelas';
    }

    public static function getModelLabel(): string
    {
        return 'Siswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Siswa Wali Kelas';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiswas::route('/'),
            'create' => CreateSiswa::route('/create'),
            'edit' => EditSiswa::route('/{record}/edit'),
        ];
    }
}
