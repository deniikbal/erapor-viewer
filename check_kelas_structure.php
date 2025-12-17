<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking tabel_kelas structure...\n";
    echo "=================================\n";
    
    // Get table columns
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'tabel_kelas' 
        ORDER BY ordinal_position
    ");
    
    if (count($columns) > 0) {
        echo "Table columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        echo "\nSample data:\n";
        $kelas = DB::table('tabel_kelas')->limit(3)->get();
        
        if ($kelas->count() > 0) {
            foreach($kelas as $k) {
                echo "Kelas: " . json_encode($k, JSON_PRETTY_PRINT) . "\n";
                break; // Just show first record
            }
        } else {
            echo "No data found in tabel_kelas table.\n";
        }
    } else {
        echo "Table tabel_kelas not found or has no columns.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}