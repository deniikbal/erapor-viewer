<?php

namespace App\Filament\Pages;

use App\Models\Sekolah;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use UnitEnum;
use BackedEnum;

class DataSekolah extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected string $view = 'filament.pages.data-sekolah';

    protected static string|UnitEnum|null $navigationGroup = 'Data Referensi';

    protected static ?string $title = 'Data Sekolah';

    public ?Sekolah $record = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->record = Sekolah::first();
        $this->form->fill($this->record ? $this->record->attributesToArray() : []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Sekolah')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nama')->label('Nama Sekolah'),
                                TextInput::make('npsn')->label('NPSN'),
                                TextInput::make('nss')->label('NSS'),
                                TextInput::make('alamat')->label('Alamat')->columnSpan(3),
                                TextInput::make('kelurahan')->label('Kelurahan'),
                                TextInput::make('kecamatan')->label('Kecamatan'),
                                TextInput::make('kd_pos')->label('Kode Pos'),
                                TextInput::make('telepon')->label('Telepon'),
                                TextInput::make('fax')->label('Fax'),
                                TextInput::make('nm_kepsek')
                                    ->label('Nama Kepala Sekolah')
                                    ->columnSpan(2)
                                    ->suffixAction(
                                        Action::make('update_nm_kepsek')
                                            ->icon('heroicon-m-pencil')
                                            ->fillForm(fn (): array => ['nm_kepsek' => $this->record->nm_kepsek])
                                            ->form([
                                                TextInput::make('nm_kepsek')
                                                    ->label('Nama Kepala Sekolah')
                                                    ->required()
                                            ])
                                            ->action(function (array $data) {
                                                $this->record->update(['nm_kepsek' => $data['nm_kepsek']]);
                                                $this->form->fill($this->record->refresh()->attributesToArray());
                                                Notification::make()->title('Saved')->success()->send();
                                            })
                                    ),
                                TextInput::make('nip_kepsek')
                                    ->label('NIP Kepala Sekolah')
                                    ->suffixAction(
                                        Action::make('update_nip_kepsek')
                                            ->icon('heroicon-m-pencil')
                                            ->fillForm(fn (): array => ['nip_kepsek' => $this->record->nip_kepsek])
                                            ->form([
                                                TextInput::make('nip_kepsek')
                                                    ->label('NIP Kepala Sekolah')
                                                    ->required()
                                            ])
                                            ->action(function (array $data) {
                                                $this->record->update(['nip_kepsek' => $data['nip_kepsek']]);
                                                $this->form->fill($this->record->refresh()->attributesToArray());
                                                Notification::make()->title('Saved')->success()->send();
                                            })
                                    ),
                            ])
                    ])
            ])
            ->statePath('data')
            ->disabled();
    }
}
