<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking existing users in database...\n";
    echo "=====================================\n";
    
    $users = DB::table('user_login')->select('id_user', 'username', 'level_user', 'status_aktif')->get();
    
    if ($users->count() > 0) {
        echo "Found " . $users->count() . " users:\n\n";
        
        foreach($users as $user) {
            echo "- ID: {$user->id_user}\n";
            echo "  Username: {$user->username}\n";
            echo "  Level: {$user->level_user}\n";
            echo "  Status: " . ($user->status_aktif ? 'Active' : 'Inactive') . "\n\n";
        }
    } else {
        echo "No users found in user_login table.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}