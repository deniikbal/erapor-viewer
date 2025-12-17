<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking tabel_siswa_pelengkap structure...\n";
    echo str_repeat("=", 60) . "\n";
    
    $pdo = DB::connection()->getPdo();
    $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'tabel_siswa_pelengkap' ORDER BY ordinal_position");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo "❌ Table tabel_siswa_pelengkap not found or empty\n";
        
        // Check if table exists
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%siswa%'");
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nTables containing 'siswa':\n";
        foreach($tables as $table) {
            echo "- " . $table['table_name'] . "\n";
        }
    } else {
        echo "✅ Found tabel_siswa_pelengkap with columns:\n";
        echo str_repeat("-", 60) . "\n";
        echo sprintf("%-30s | %s\n", "Column Name", "Data Type");
        echo str_repeat("-", 60) . "\n";
        
        foreach($columns as $col) {
            echo sprintf("%-30s | %s\n", $col['column_name'], $col['data_type']);
        }
        
        // Sample data
        echo "\nSample data (first 3 rows):\n";
        echo str_repeat("-", 60) . "\n";
        $stmt = $pdo->query("SELECT * FROM tabel_siswa_pelengkap LIMIT 3");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($samples)) {
            $firstRow = $samples[0];
            foreach($firstRow as $key => $value) {
                echo sprintf("%-30s: %s\n", $key, $value ?? 'NULL');
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}