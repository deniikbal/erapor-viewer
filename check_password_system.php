<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Analyzing password system...\n";
    echo "===========================\n";
    
    $user = DB::table('user_login')->where('userid', 'silmi')->first();
    
    if ($user) {
        echo "User: {$user->nama}\n";
        echo "Password hash length: " . strlen($user->password) . " characters\n";
        echo "Salt length: " . strlen($user->salt) . " characters\n";
        echo "Password sample: " . substr($user->password, 0, 20) . "...\n";
        echo "Salt sample: " . substr($user->salt, 0, 20) . "...\n";
        
        echo "\nThis appears to be a custom hash system.\n";
        echo "For testing purposes, we need to either:\n";
        echo "1. Create a new user with Laravel hash\n";
        echo "2. Implement the original hash verification\n";
        echo "3. Reset password for existing user\n";
        
    } else {
        echo "User not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}