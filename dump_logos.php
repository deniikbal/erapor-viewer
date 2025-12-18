<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = \DB::table('tambah.logo_ttdkepsek')->get();
file_put_contents('logo_data_dump.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Data dumped to logo_data_dump.json\n";
