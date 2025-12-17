<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking tabel_siswa structure...\n";
    echo "=================================\n";
    
    // Get table columns
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'tabel_siswa' 
        ORDER BY ordinal_position
    ");
    
    if (count($columns) > 0) {
        echo "Table columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        echo "\nSample data:\n";
        $siswa = DB::table('tabel_siswa')->limit(1)->get();
        
        if ($siswa->count() > 0) {
            foreach($siswa as $s) {
                echo "Siswa: " . json_encode($s, JSON_PRETTY_PRINT) . "\n";
                break;
            }
        } else {
            echo "No data found in tabel_siswa table.\n";
        }
    } else {
        echo "Table tabel_siswa not found or has no columns.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}