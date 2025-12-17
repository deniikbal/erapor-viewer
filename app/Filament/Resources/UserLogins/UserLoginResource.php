<?php

namespace App\Filament\Resources\UserLogins;

use App\Filament\Resources\UserLogins\Pages\CreateUserLogin;
use App\Filament\Resources\UserLogins\Pages\EditUserLogin;
use App\Filament\Resources\UserLogins\Pages\ListUserLogins;
use App\Filament\Resources\UserLogins\Schemas\UserLoginForm;
use App\Filament\Resources\UserLogins\Tables\UserLoginsTable;
use App\Models\UserLogin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserLoginResource extends Resource
{
    protected static ?string $model = UserLogin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserLoginForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserLoginsTable::configure($table);
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
            'index' => ListUserLogins::route('/'),
            'create' => CreateUserLogin::route('/create'),
            'edit' => EditUserLogin::route('/{record}/edit'),
        ];
    }
}
