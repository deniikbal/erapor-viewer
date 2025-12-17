<?php

namespace App\Filament\Resources\Ptks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PtksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ptk_id'),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('jenis_ptk_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->searchable(),
                TextColumn::make('tempat_lahir')
                    ->searchable(),
                TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('nuptk')
                    ->searchable(),
                TextColumn::make('alamat_jalan')
                    ->searchable(),
                TextColumn::make('status_keaktifan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('soft_delete')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
