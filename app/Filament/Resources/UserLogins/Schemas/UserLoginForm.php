<?php

namespace App\Filament\Resources\UserLogins\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserLoginForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('userid')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('level')
                    ->required(),
                TextInput::make('ptk_id'),
                TextInput::make('salt'),
                TextInput::make('is_logged_in')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('tgl_daftar'),
                DateTimePicker::make('last_logged_in'),
                TextInput::make('add_by')
                    ->numeric(),
                TextInput::make('ip_address'),
                TextInput::make('photo'),
                TextInput::make('status_edit')
                    ->numeric(),
                TextInput::make('is_active')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('sub_you')
                    ->numeric()
                    ->default(0),
                TextInput::make('id')
                    ->label('ID')
                    ->required()
                    ->default('uuid_generate_v4()'),
                DateTimePicker::make('last_logged_out'),
                TextInput::make('thema')
                    ->default('sma-theme'),
                TextInput::make('warnaheader')
                    ->default(''),
                TextInput::make('warnaside')
                    ->default(''),
            ]);
    }
}
