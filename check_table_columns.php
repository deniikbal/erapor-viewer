<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Illuminate\Support\Facades\DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_schema='public' AND table_name='tabel_sekolah' ORDER BY column_name");
$output = "";
foreach ($cols as $c) {
    $output .= $c->column_name . "\n";
}
file_put_contents('columns_sekolah.txt', $output);
echo "Columns dumped to columns_sekolah.txt\n";
