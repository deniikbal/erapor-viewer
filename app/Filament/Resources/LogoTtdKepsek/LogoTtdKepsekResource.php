<?php

namespace App\Filament\Resources\LogoTtdKepsek;

use App\Filament\Resources\LogoTtdKepsek\Pages\CreateLogoTtdKepsek;
use App\Filament\Resources\LogoTtdKepsek\Pages\EditLogoTtdKepsek;
use App\Filament\Resources\LogoTtdKepsek\Pages\ListLogoTtdKepseks;
use App\Models\LogoTtdKepsek;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use App\Models\Sekolah;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;

class LogoTtdKepsekResource extends Resource
{
    protected static ?string $model = LogoTtdKepsek::class;

    protected static string|UnitEnum|null $navigationGroup = 'Data Referensi';

    protected static ?string $navigationLabel = 'Data Logo & TTD';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Upload Logo dan Tanda Tangan')
                ->schema([
                    Select::make('sekolah_id')
                        ->label('Sekolah')
                        ->options(Sekolah::query()->pluck('nama', 'sekolah_id'))
                        ->searchable()
                        ->required()
                        ->default(fn () => auth()->user()->sekolah_id)
                        ->helperText('Pilih Sekolah. Jika kosong, pastikan master data sekolah sudah terisi.'),
                    
                    Grid::make(2)->schema([
                        FileUpload::make('logo_pemda')
                            ->label('Logo Pemda')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->imageEditor()
                            ->columnSpan(1),
                        
                        FileUpload::make('logo_sek') // Column name verified as 'logo_sek'
                            ->label('Logo Sekolah')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->imageEditor()
                            ->columnSpan(1),
                            
                        FileUpload::make('ttd_kepsek')
                            ->label('TTD Kepala Sekolah')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->imageEditor()
                            ->columnSpan(1),
                            
                        FileUpload::make('kop_sekolah')
                            ->label('Kop Sekolah')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->imageEditor()
                            ->columnSpan(1),
                    ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sekolah.nama')->label('Nama Sekolah')->searchable(),
                ImageColumn::make('logo_pemda')->label('Logo Pemda')->disk('public'),
                ImageColumn::make('logo_sek')->label('Logo Sekolah')->disk('public'),
                ImageColumn::make('ttd_kepsek')->label('TTD Kepsek')->disk('public'),
                ImageColumn::make('kop_sekolah')->label('Kop Sekolah')->disk('public'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogoTtdKepseks::route('/'),
            'create' => CreateLogoTtdKepsek::route('/create'),
            'edit' => EditLogoTtdKepsek::route('/{record}/edit'),
        ];
    }
}
