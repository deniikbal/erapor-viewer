<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schema = 'tambah';
$outputFile = __DIR__ . '/backup_tambah_data.sql';
$handle = fopen($outputFile, 'w');

if (!$handle) {
    die("Could not open file for writing.\n");
}

fwrite($handle, "-- Backup data schema '$schema'\n");
fwrite($handle, "-- Generated at " . date('Y-m-d H:i:s') . "\n\n");
fwrite($handle, "SET search_path = $schema, public;\n\n");

// Get tables in correct order is hard due to FKs, but usually for data restore we disable constraints or user handles it.
// We will list them alphabetical.
$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name", [$schema]);

foreach ($tables as $t) {
    $tableName = $t->table_name;
    $fullTableName = $schema . '.' . $tableName;
    
    echo "Processing table: $fullTableName\n";
    fwrite($handle, "-- Data for table $fullTableName\n");
    
    $cols = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position", [$schema, $tableName]);
    if (empty($cols)) {
        fwrite($handle, "-- No columns found (view?)\n\n");
        continue;
    }

    $colNames = array_map(function($c) { return $c->column_name; }, $cols);
    $colList = implode(', ', array_map(function($c) { return '"' . $c . '"'; }, $colNames));
    
    // Check if table has rows
    $count = DB::table($fullTableName)->count();
    if ($count == 0) {
         fwrite($handle, "-- No data\n\n");
         continue;
    }

    // Get PK for ON CONFLICT clause
    $pks = DB::select("
        SELECT kcu.column_name
        FROM information_schema.key_column_usage kcu
        JOIN information_schema.table_constraints tc
          ON kcu.constraint_name = tc.constraint_name
          AND kcu.table_schema = tc.table_schema
        WHERE kcu.table_schema = ?
          AND kcu.table_name = ?
          AND tc.constraint_type = 'PRIMARY KEY'
        ORDER BY kcu.ordinal_position
    ", [$schema, $tableName]);

    $pkClause = "";
    if (!empty($pks)) {
        $pkCols = array_map(function($p) { return '"' . $p->column_name . '"'; }, $pks);
        $pkClause = "ON CONFLICT (" . implode(', ', $pkCols) . ") DO NOTHING";
    }

    $query = DB::table($fullTableName);
    
    // Cursor for memory efficiency
    foreach ($query->cursor() as $row) {
        $values = [];
        foreach ($colNames as $col) {
            $val = $row->$col;
            if ($val === null) {
                $values[] = 'NULL';
            } elseif (is_bool($val)) {
                $values[] = $val ? 'TRUE' : 'FALSE';
            } elseif (is_int($val) || is_float($val)) {
                $values[] = $val;
            } else {
                 // Escape string utilizing PDO quote which handles underlying driver specific escaping
                 $values[] = DB::connection()->getPdo()->quote((string)$val);
            }
        }
        
        $sql = "INSERT INTO $schema.\"$tableName\" ($colList) VALUES (" . implode(', ', $values) . ") $pkClause;\n";
        fwrite($handle, $sql);
    }
    fwrite($handle, "\n");
}

fclose($handle);
echo "Backup completed: $outputFile\n";
