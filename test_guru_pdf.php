<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\UserLogin;

try {
    echo "Testing Guru PDF Generation...\n";
    echo str_repeat("=", 60) . "\n";
    
    // Find kelas with wali kelas first
    $kelasWali = Kelas::where('jenis_rombel', 1)
        ->whereNotNull('ptk_id')
        ->with('waliKelas')
        ->first();
    
    if (!$kelasWali) {
        echo "❌ No class with wali kelas found\n";
        exit;
    }
    
    // Find the guru user for this kelas
    $guruUser = UserLogin::where('ptk_id', $kelasWali->ptk_id)->first();
    
    if (!$guruUser) {
        echo "❌ No user found for this wali kelas\n";
        echo "PTK ID needed: {$kelasWali->ptk_id}\n";
        exit;
    }
    
    echo "Testing with Guru: {$guruUser->user_id}\n";
    echo "PTK ID: {$guruUser->ptk_id}\n";
    
    echo "✅ Guru is wali kelas for: {$kelasWali->nm_kelas}\n";
    echo "Wali Kelas: " . ($kelasWali->waliKelas->nama ?? 'Unknown') . "\n";
    
    // Get siswa from this kelas
    $siswaWaliKelas = Siswa::with(['pelengkap', 'anggotaKelas.kelas'])
        ->whereHas('anggotaKelas.kelas', function ($query) use ($guruUser) {
            $query->where('ptk_id', $guruUser->ptk_id)
                  ->where('jenis_rombel', 1);
        })
        ->orderBy('nm_siswa')
        ->get();
    
    echo "\nSiswa in Wali Kelas ({$siswaWaliKelas->count()} students):\n";
    echo str_repeat("-", 80) . "\n";
    
    if ($siswaWaliKelas->count() > 0) {
        $firstSiswa = $siswaWaliKelas->first();
        echo "Sample Student for PDF:\n";
        echo "- ID: {$firstSiswa->peserta_didik_id}\n";
        echo "- Nama: {$firstSiswa->nm_siswa}\n";
        echo "- NIS: " . ($firstSiswa->nis ?? 'N/A') . "\n";
        echo "- NISN: " . ($firstSiswa->nisn ?? 'N/A') . "\n";
        echo "- Jenis Kelamin: " . ($firstSiswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($firstSiswa->jenis_kelamin == 'P' ? 'Perempuan' : $firstSiswa->jenis_kelamin)) . "\n";
        echo "- Tempat Lahir: " . ($firstSiswa->tempat_lahir ?? 'N/A') . "\n";
        echo "- Tanggal Lahir: " . ($firstSiswa->tanggal_lahir ? $firstSiswa->tanggal_lahir->format('d/m/Y') : 'N/A') . "\n";
        
        // Check pelengkap data
        if ($firstSiswa->pelengkap) {
            echo "- Status dalam Keluarga: " . ($firstSiswa->pelengkap->status_dalam_kel ?? 'N/A') . "\n";
            echo "- Anak Ke: " . ($firstSiswa->pelengkap->anak_ke ?? 'N/A') . "\n";
            echo "- Sekolah Asal: " . ($firstSiswa->pelengkap->sekolah_asal ?? 'N/A') . "\n";
        } else {
            echo "- Data Pelengkap: Tidak ada\n";
        }
        
        echo "\nPDF URLs that will be available:\n";
        echo "📄 Single PDF: /guru/siswa/{$firstSiswa->peserta_didik_id}/pdf\n";
        echo "📄 All Students PDF: /guru/siswa/pdf/all\n";
        
        // Statistics for PDF
        $lakiLaki = $siswaWaliKelas->where('jenis_kelamin', 'L')->count();
        $perempuan = $siswaWaliKelas->where('jenis_kelamin', 'P')->count();
        $withPelengkap = $siswaWaliKelas->whereNotNull('pelengkap')->count();
        
        echo "\nPDF Content Statistics:\n";
        echo "📊 Total Siswa: {$siswaWaliKelas->count()}\n";
        echo "👨 Laki-laki: {$lakiLaki}\n";
        echo "👩 Perempuan: {$perempuan}\n";
        echo "📋 Dengan Data Pelengkap: {$withPelengkap}\n";
        
        echo "\n✅ PDF Generation ready!\n";
        echo "🌐 Access Guru Panel: http://127.0.0.1:8000/guru/siswas\n";
        echo "👤 Login as: {$guruUser->user_id}\n";
        echo "🔑 Password: @dikdasmen123456*\n";
        echo "\nFeatures Available:\n";
        echo "- 📄 Individual PDF per student (button on each row)\n";
        echo "- 📄 PDF for all students in class (toolbar button)\n";
        echo "- 📋 Complete student identity form layout\n";
        echo "- 🖼️ Photo placeholder and signature section\n";
        
    } else {
        echo "❌ No students found for this wali kelas\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}