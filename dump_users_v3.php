<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \DB::table('public.user_login')->take(5)->get();
file_put_contents('users_data.json', json_encode($users, JSON_PRETTY_PRINT));
