<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking tabel_kurikulum structure...\n";
    echo "====================================\n";
    
    // Check if tabel_kurikulum exists
    $kurikulumExists = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'tabel_kurikulum')");
    
    if ($kurikulumExists[0]->exists) {
        $columns = DB::select("
            SELECT column_name, data_type 
            FROM information_schema.columns 
            WHERE table_name = 'tabel_kurikulum' 
            ORDER BY ordinal_position
        ");
        
        echo "tabel_kurikulum columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        $sample = DB::table('tabel_kurikulum')->limit(3)->get();
        if ($sample->count() > 0) {
            echo "\nSample data:\n";
            foreach($sample as $k) {
                echo "- ID: {$k->kurikulum_id}, Nama: " . (isset($k->nama_kurikulum) ? $k->nama_kurikulum : 'N/A') . "\n";
            }
        }
    } else {
        echo "❌ tabel_kurikulum not found\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Checking tabel_anggotakelas structure...\n";
    echo "=======================================\n";
    
    $anggotaExists = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'tabel_anggotakelas')");
    
    if ($anggotaExists[0]->exists) {
        $columns = DB::select("
            SELECT column_name, data_type 
            FROM information_schema.columns 
            WHERE table_name = 'tabel_anggotakelas' 
            ORDER BY ordinal_position
        ");
        
        echo "tabel_anggotakelas columns:\n";
        foreach($columns as $column) {
            echo "- {$column->column_name} ({$column->data_type})\n";
        }
        
        $sample = DB::table('tabel_anggotakelas')->limit(3)->get();
        if ($sample->count() > 0) {
            echo "\nSample data:\n";
            foreach($sample as $a) {
                echo "Sample: " . json_encode($a, JSON_PRETTY_PRINT) . "\n";
                break;
            }
        }
        
        // Count students per class
        echo "\nStudent count per class (sample):\n";
        $counts = DB::table('tabel_anggotakelas')
            ->select('rombongan_belajar_id', DB::raw('COUNT(*) as jumlah_siswa'))
            ->groupBy('rombongan_belajar_id')
            ->limit(5)
            ->get();
            
        foreach($counts as $count) {
            echo "- Class ID: {$count->rombongan_belajar_id}, Students: {$count->jumlah_siswa}\n";
        }
        
    } else {
        echo "❌ tabel_anggotakelas not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}