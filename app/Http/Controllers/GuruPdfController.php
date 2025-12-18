<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\LogoTtdKepsek;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruPdfController extends Controller
{
    public function getSiswaData($siswaId)
    {
        return response()->json($this->fetchSiswaData($siswaId));
    }

    private function fetchSiswaData($siswaId)
    {
        $user = Auth::user();
        
        // Get siswa data with validation that it belongs to guru's class
        $siswa = Siswa::with(['pelengkap', 'anggotaKelas.kelas'])
            ->whereHas('anggotaKelas.kelas', function ($query) use ($user) {
                $query->where('ptk_id', $user->ptk_id)
                      ->where('jenis_rombel', 1);
            })
            ->findOrFail($siswaId);
        
        // Get kelas info
        $kelas = $siswa->anggotaKelas()
            ->with('kelas')
            ->whereHas('kelas', function ($query) use ($user) {
                $query->where('ptk_id', $user->ptk_id)
                      ->where('jenis_rombel', 1);
            })
            ->first();
        
        $kelasInfo = $kelas ? $kelas->kelas : null;

        // Get Logo & TTD Data
        $sekolahId = $user->sekolah_id; 
        $logoData = LogoTtdKepsek::where('sekolah_id', $sekolahId)->first();

        return [
            'siswa' => [
                'id' => $siswa->peserta_didik_id,
                'full_name' => $siswa->nm_siswa,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'birth_place' => $siswa->tempat_lahir,
                'birth_date' => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : null,
                'gender' => $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : $siswa->jenis_kelamin),
                'religion' => $siswa->agama,
                'address' => $siswa->alamat_siswa,
                'phone_number' => $siswa->telepon_siswa,
                'father_name' => $siswa->nm_ayah,
                'mother_name' => $siswa->nm_ibu,
                'father_job' => $siswa->pekerjaan_ayah,
                'mother_job' => $siswa->pekerjaan_ibu,
                'guardian_name' => $siswa->nm_wali,
                'guardian_job' => $siswa->pekerjaan_wali,
                // Data pelengkap
                'family_status' => $siswa->pelengkap->status_dalam_kel ?? null,
                'child_number' => $siswa->pelengkap->anak_ke ?? null,
                'previous_school' => $siswa->pelengkap->sekolah_asal ?? null,
                'accepted_class' => $siswa->pelengkap->diterima_kelas ?? 'X',
                'parent_address' => $siswa->pelengkap->alamat_ortu ?? null,
                'parent_phone' => $siswa->pelengkap->telepon_ortu ?? null,
                'guardian_address' => $siswa->pelengkap->alamat_wali ?? null,
                'guardian_phone' => $siswa->pelengkap->telepon_wali ?? null,
            ],
            'kelas' => $kelasInfo ? [
                'nama' => $kelasInfo->nm_kelas,
                'wali_kelas' => $kelasInfo->waliKelas->nama ?? null
            ] : null,
            'kepala_sekolah' => [
                'nama' => 'Dr. H. Toto Warsito, S.Ag., M.Ag.',
                'nip' => '19730302 199802 1 002'
            ],
            'logos' => $logoData ? [
                'logo_pemda' => $logoData->logo_pemda ? '/storage/' . $logoData->logo_pemda : null,
                'logo_sekolah' => $logoData->logo_sek ? '/storage/' . $logoData->logo_sek : null,
                'ttd_kepsek' => $logoData->ttd_kepsek ? '/storage/' . $logoData->ttd_kepsek : null,
                'kop_sekolah' => $logoData->kop_sekolah ? '/storage/' . $logoData->kop_sekolah : null,
            ] : null
        ];
    }
    
    public function downloadPdf($siswaId)
    {
        $data = $this->fetchSiswaData($siswaId);
        
        $pdf = Pdf::loadView('pdf.identitas-siswa', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Identitas_' . str_replace(' ', '_', $data['siswa']['full_name']) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function showPdfPage($siswaId = null)
    {
        return view('pdf.generator', ['siswaId' => $siswaId]);
    }
    
    public function getAllSiswaData()
    {
        $user = Auth::user();
        
        // Get all siswa from guru's class
        // ... (query same as original) ...
        $siswaList = Siswa::with(['pelengkap', 'anggotaKelas.kelas'])
            ->whereHas('anggotaKelas.kelas', function ($query) use ($user) {
                $query->where('ptk_id', $user->ptk_id)
                      ->where('jenis_rombel', 1);
            })
            ->orderBy('nm_siswa')
            ->get();
        
        if ($siswaList->isEmpty()) {
            return response()->json(['error' => 'Tidak ada siswa untuk dicetak PDF'], 404);
        }
        
        // Get kelas info
        $kelas = Kelas::where('ptk_id', $user->ptk_id)
            ->where('jenis_rombel', 1)
            ->first();

        // Get Logo Data
        $sekolahId = $user->sekolah_id;
        $logoData = LogoTtdKepsek::where('sekolah_id', $sekolahId)->first();
        $logos = $logoData ? [
            'logo_pemda' => $logoData->logo_pemda ? '/storage/' . $logoData->logo_pemda : null,
            'logo_sekolah' => $logoData->logo_sek ? '/storage/' . $logoData->logo_sek : null,
            'ttd_kepsek' => $logoData->ttd_kepsek ? '/storage/' . $logoData->ttd_kepsek : null,
            'kop_sekolah' => $logoData->kop_sekolah ? '/storage/' . $logoData->kop_sekolah : null,
        ] : null;
        
        // Format data for JavaScript PDF generation
        $data = [
            'siswaList' => $siswaList->map(function ($siswa) {
                return [
                    'id' => $siswa->peserta_didik_id,
                    'full_name' => $siswa->nm_siswa,
                    'nis' => $siswa->nis,
                    'nisn' => $siswa->nisn,
                    'birth_place' => $siswa->tempat_lahir,
                    'birth_date' => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : null,
                    'gender' => $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : $siswa->jenis_kelamin),
                    'religion' => $siswa->agama,
                    'address' => $siswa->alamat_siswa,
                    'phone_number' => $siswa->telepon_siswa,
                    'father_name' => $siswa->nm_ayah,
                    'mother_name' => $siswa->nm_ibu,
                    'father_job' => $siswa->pekerjaan_ayah,
                    'mother_job' => $siswa->pekerjaan_ibu,
                    'guardian_name' => $siswa->nm_wali,
                    'guardian_job' => $siswa->pekerjaan_wali,
                    // Data pelengkap
                    'family_status' => $siswa->pelengkap->status_dalam_kel ?? null,
                    'child_number' => $siswa->pelengkap->anak_ke ?? null,
                    'previous_school' => $siswa->pelengkap->sekolah_asal ?? null,
                    'accepted_class' => $siswa->pelengkap->diterima_kelas ?? 'X',
                    'parent_address' => $siswa->pelengkap->alamat_ortu ?? null,
                    'parent_phone' => $siswa->pelengkap->telepon_ortu ?? null,
                    'guardian_address' => $siswa->pelengkap->alamat_wali ?? null,
                    'guardian_phone' => $siswa->pelengkap->telepon_wali ?? null,
                ];
            }),
            'kelas' => $kelas ? [
                'nama' => $kelas->nm_kelas,
                'wali_kelas' => $kelas->waliKelas->nama ?? null
            ] : null,
            'kepala_sekolah' => [
                'nama' => 'Dr. H. Toto Warsito, S.Ag., M.Ag.',
                'nip' => '19730302 199802 1 002'
            ],
            'logos' => $logos
        ];
        
        return response()->json($data);
    }
}