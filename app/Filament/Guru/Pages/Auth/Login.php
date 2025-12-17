<?php

namespace App\Filament\Guru\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('userid')
                    ->label('User ID')
                    ->placeholder('Masukkan User ID Guru (contoh: 199404162024212033)')
                    ->required()
                    ->autocomplete('username')
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'userid' => $data['userid'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.userid' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}