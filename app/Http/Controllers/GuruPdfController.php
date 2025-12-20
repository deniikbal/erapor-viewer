<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\LogoTtdKepsek;
use App\Models\Sekolah;
use App\Models\TanggalRapor;
use App\Models\Semester;

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
        $logoData = LogoTtdKepsek::first();
        
        // Get School Data
        $sekolah = Sekolah::first();
        
        // Get TanggalRapor Data
        $tanggalRapor = TanggalRapor::first();
        
        // Get Semester Data
        $semester = Semester::where('periode_aktif', 1)->first();

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
                'diterima_tanggal' => $siswa->diterima_tanggal ? $siswa->diterima_tanggal->format('Y-m-d') : null,
            ],
            'kelas' => $kelasInfo ? [
                'nama' => $kelasInfo->nm_kelas,
                'wali_kelas' => $kelasInfo->waliKelas->nama ?? null
            ] : null,
            'sekolah' => $sekolah ? [
                'nama' => $sekolah->nama,
                'npsn' => $sekolah->npsn,
                'nss' => $sekolah->nss,
                'alamat' => $sekolah->alamat,
                'kelurahan' => $sekolah->kelurahan,
                'kecamatan' => $sekolah->kecamatan,
                'kab_kota' => $sekolah->kab_kota,
                'propinsi' => $sekolah->propinsi,
                'website' => $sekolah->website,
                'email' => $sekolah->email,
                'nm_kepsek' => $sekolah->nm_kepsek,
                'nip_kepsek' => $sekolah->nip_kepsek
            ] : null,
            'tanggal_rapor' => $tanggalRapor ? [
                'tempat_ttd' => $tanggalRapor->tempat_ttd,
                'tanggal' => $tanggalRapor->tanggal ? $tanggalRapor->tanggal->format('Y-m-d') : null
            ] : null,
            'semester' => $semester ? [
                'semester_id' => $semester->semester_id,
                'nama_semester' => $semester->nama_semester,
                'semester' => $semester->semester,
                'tahun_ajaran_id' => $semester->tahun_ajaran_id
            ] : null,
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
    
    public function streamPdf($siswaId)
    {
        $data = $this->fetchSiswaData($siswaId);
        
        $pdf = Pdf::loadView('pdf.identitas-siswa', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Identitas_' . str_replace(' ', '_', $data['siswa']['full_name']) . '.pdf');
    }
    
    public function showPdfPage($siswaId = null)
    {
        return view('pdf.generator', ['siswaId' => $siswaId]);
    }
    
    public function showPdfPreview($siswaId = null)
    {
        return view('pdf.generator', ['siswaId' => $siswaId, 'previewMode' => true]);
    }
    
    public function showLaporanHasilBelajar($siswaId = null)
    {
        return view('pdf.laporan-hasil-belajar', ['siswaId' => $siswaId]);
    }
    
    public function showLaporanHasilBelajarPreview($siswaId = null)
    {
        return view('pdf.laporan-hasil-belajar', ['siswaId' => $siswaId, 'previewMode' => true]);
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
        $logoData = LogoTtdKepsek::first();
        
        // Get School Data
        $sekolah = Sekolah::first();
        
        // Get TanggalRapor Data
        $tanggalRapor = TanggalRapor::first();
        
        // Get Semester Data
        $semester = Semester::where('periode_aktif', 1)->first();
        
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
                    'diterima_tanggal' => $siswa->diterima_tanggal ? $siswa->diterima_tanggal->format('Y-m-d') : null,
                ];
            }),
            'kelas' => $kelas ? [
                'nama' => $kelas->nm_kelas,
                'wali_kelas' => $kelas->waliKelas->nama ?? null
            ] : null,
            'sekolah' => $sekolah ? [
                'nama' => $sekolah->nama,
                'npsn' => $sekolah->npsn,
                'nss' => $sekolah->nss,
                'alamat' => $sekolah->alamat,
                'kelurahan' => $sekolah->kelurahan,
                'kecamatan' => $sekolah->kecamatan,
                'kab_kota' => $sekolah->kab_kota,
                'propinsi' => $sekolah->propinsi,
                'website' => $sekolah->website,
                'email' => $sekolah->email,
                'nm_kepsek' => $sekolah->nm_kepsek,
                'nip_kepsek' => $sekolah->nip_kepsek
            ] : null,
            'tanggal_rapor' => $tanggalRapor ? [
                'tempat_ttd' => $tanggalRapor->tempat_ttd,
                'tanggal' => $tanggalRapor->tanggal ? $tanggalRapor->tanggal->format('Y-m-d') : null
            ] : null,
            'semester' => $semester ? [
                'semester_id' => $semester->semester_id,
                'nama_semester' => $semester->nama_semester,
                'semester' => $semester->semester,
                'tahun_ajaran_id' => $semester->tahun_ajaran_id
            ] : null,
            'logos' => $logos
        ];
        
        return response()->json($data);
    }
}