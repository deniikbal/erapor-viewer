<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking user_login table structure...\n";
    echo "=====================================\n";
    
    // Get table columns
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'user_login' 
        ORDER BY ordinal_position
    ");
    
    if (count($columns) > 0) {
        echo "Table columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        echo "\nSample data:\n";
        $users = DB::table('user_login')->limit(5)->get();
        
        if ($users->count() > 0) {
            foreach($users as $user) {
                echo "User: " . json_encode($user, JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "No data found in user_login table.\n";
        }
    } else {
        echo "Table user_login not found or has no columns.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}