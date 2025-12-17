<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking tabel_ptk structure...\n";
    echo "===============================\n";
    
    // Get table columns
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'tabel_ptk' 
        ORDER BY ordinal_position
    ");
    
    if (count($columns) > 0) {
        echo "Table columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        echo "\nSample data:\n";
        $ptk = DB::table('tabel_ptk')->limit(1)->get();
        
        if ($ptk->count() > 0) {
            foreach($ptk as $p) {
                echo "PTK: " . json_encode($p, JSON_PRETTY_PRINT) . "\n";
                break;
            }
        } else {
            echo "No data found in tabel_ptk table.\n";
        }
    } else {
        echo "Table tabel_ptk not found or has no columns.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}