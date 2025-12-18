<?php

namespace App\Filament\Guru\Resources\NilaiAkhirs;

use App\Filament\Guru\Resources\NilaiAkhirs\Pages\CreateNilaiAkhir;
use App\Filament\Guru\Resources\NilaiAkhirs\Pages\EditNilaiAkhir;
use App\Filament\Guru\Resources\NilaiAkhirs\Pages\ListNilaiAkhirs;
use App\Filament\Guru\Resources\NilaiAkhirs\Schemas\NilaiAkhirForm;
use App\Filament\Guru\Resources\NilaiAkhirs\Tables\NilaiAkhirsTable;
use App\Models\NilaiAkhir;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NilaiAkhirResource extends Resource
{
    protected static ?string $model = NilaiAkhir::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Nilai Akhir Siswa';

    protected static ?string $modelLabel = 'Nilai Akhir';

    protected static ?string $pluralModelLabel = 'Nilai Akhir Siswa';

    public static function form(Schema $schema): Schema
    {
        return NilaiAkhirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaiAkhirsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNilaiAkhirs::route('/'),
            'create' => CreateNilaiAkhir::route('/create'),
            'edit' => EditNilaiAkhir::route('/{record}/edit'),
        ];
    }
}
