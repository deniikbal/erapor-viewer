<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\GuruPdfController;
use App\Models\Siswa;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Auth;

echo "=== Testing API Response ===\n";

// Get a user and login
$user = UserLogin::first();
if (!$user) {
    echo "✗ No user found\n";
    exit(1);
}

Auth::login($user);
echo "✓ User logged in: " . $user->nama . "\n";

// Get a siswa
$siswa = Siswa::first();
if (!$siswa) {
    echo "✗ No siswa found\n";
    exit(1);
}

echo "✓ Testing with siswa: " . $siswa->nm_siswa . "\n";

// Test controller method
try {
    $controller = new GuruPdfController();
    $response = $controller->getSiswaData($siswa->peserta_didik_id);
    $data = $response->getData();
    
    echo "✓ API response generated\n";
    
    if (isset($data->logos)) {
        echo "✓ Logos data present:\n";
        echo "  - Logo Pemda: " . ($data->logos->logo_pemda ?? 'NULL') . "\n";
        echo "  - Logo Sekolah: " . ($data->logos->logo_sekolah ?? 'NULL') . "\n";
        echo "  - TTD Kepsek: " . ($data->logos->ttd_kepsek ?? 'NULL') . "\n";
        echo "  - Kop Sekolah: " . ($data->logos->kop_sekolah ?? 'NULL') . "\n";
    } else {
        echo "✗ No logos data in response\n";
    }
    
    if (isset($data->siswa)) {
        echo "✓ Siswa data present: " . $data->siswa->full_name . "\n";
    } else {
        echo "✗ No siswa data in response\n";
    }
    
} catch (Exception $e) {
    echo "✗ API test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";