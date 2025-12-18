<?php

namespace App\Filament\Resources\Ptks;

use App\Filament\Resources\Ptks\Pages\CreatePtk;
use App\Filament\Resources\Ptks\Pages\EditPtk;
use App\Filament\Resources\Ptks\Pages\ListPtks;
use App\Filament\Resources\Ptks\Schemas\PtkForm;
use App\Filament\Resources\Ptks\Tables\PtksTable;
use App\Models\Ptk;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PtkResource extends Resource
{
    protected static ?string $model = Ptk::class;

    protected static string|UnitEnum|null $navigationGroup = 'Data Referensi';

    protected static ?string $navigationLabel = 'Data Guru';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PtkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PtksTable::configure($table);
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
            'index' => ListPtks::route('/'),
            'create' => CreatePtk::route('/create'),
            'edit' => EditPtk::route('/{record}/edit'),
        ];
    }
}
