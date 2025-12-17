<?php

namespace App\Filament\Resources\UserLogins\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserLoginsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('userid')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('level')
                    ->searchable(),
                TextColumn::make('ptk_id'),
                TextColumn::make('salt')
                    ->searchable(),
                TextColumn::make('is_logged_in')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tgl_daftar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_logged_in')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('add_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->searchable(),
                TextColumn::make('photo')
                    ->searchable(),
                TextColumn::make('status_edit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('sub_you')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('last_logged_out')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('thema')
                    ->searchable(),
                TextColumn::make('warnaheader')
                    ->searchable(),
                TextColumn::make('warnaside')
                    ->searchable(),
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
