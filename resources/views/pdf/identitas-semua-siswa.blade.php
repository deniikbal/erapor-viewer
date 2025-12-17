<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Identitas Semua Siswa - {{ $kelas->nm_kelas ?? 'Kelas' }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 15mm;
        }
        
        .kelas-info {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 10mm;
        }
        
        .content {
            margin-left: 0;
        }
        
        .row {
            display: flex;
            margin-bottom: 2mm;
            align-items: flex-start;
        }
        
        .no {
            width: 8mm;
            flex-shrink: 0;
        }
        
        .label {
            width: 60mm;
            flex-shrink: 0;
        }
        
        .colon {
            width: 5mm;
            flex-shrink: 0;
        }
        
        .value {
            flex: 1;
            word-wrap: break-word;
        }
        
        .sub-item {
            margin-left: 8mm;
        }
        
        .signature-section {
            margin-top: 15mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .photo-section {
            width: 35mm;
            text-align: center;
        }
        
        .photo-placeholder {
            width: 30mm;
            height: 40mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #666;
        }
        
        .signature {
            text-align: center;
            width: 80mm;
        }
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 20mm;
            margin-bottom: 2mm;
        }
        
        .capitalize {
            text-transform: capitalize;
        }
        
        .uppercase {
            text-transform: uppercase;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .student-header {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8mm;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
        }
    </style>
</head>
<body>
    <div class="header">
        IDENTITAS PESERTA DIDIK
    </div>
    
    @if($kelas)
    <div class="kelas-info">
        Kelas: {{ $kelas->nm_kelas }}
        @if($kelas->waliKelas)
            | Wali Kelas: {{ $kelas->waliKelas->nama }}
        @endif
    </div>
    @endif
    
    @foreach($siswaList as $index => $siswa)
        @if($index > 0)
            <div class="page-break"></div>
            <div class="header">
                IDENTITAS PESERTA DIDIK
            </div>
        @endif
        
        <div class="student-header">
            {{ $index + 1 }}. {{ $siswa->nm_siswa }}
        </div>
        
        <div class="content">
            <div class="row">
                <div class="no">1.</div>
                <div class="label">Nama Lengkap Peserta Didik</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->nm_siswa ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">2.</div>
                <div class="label">Nomor Induk/NISN</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->nis ?? '' }} / {{ $siswa->nisn ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">3.</div>
                <div class="label">Tempat, Tanggal Lahir</div>
                <div class="colon">:</div>
                <div class="value">
                    @if($siswa->tempat_lahir || $siswa->tanggal_lahir)
                        {{ $siswa->tempat_lahir ?? '' }}@if($siswa->tempat_lahir && $siswa->tanggal_lahir), @endif
                        @if($siswa->tanggal_lahir)
                            {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}
                        @endif
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="no">4.</div>
                <div class="label">Jenis Kelamin</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : $siswa->jenis_kelamin) }}</div>
            </div>
            
            <div class="row">
                <div class="no">5.</div>
                <div class="label">Agama</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->agama ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">6.</div>
                <div class="label">Status dalam Keluarga</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->status_dalam_kel ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">7.</div>
                <div class="label">Anak ke</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->anak_ke ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">8.</div>
                <div class="label">Alamat Peserta Didik</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->alamat_siswa ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">9.</div>
                <div class="label">Nomor Telepon Rumah</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->telepon_siswa ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">10.</div>
                <div class="label">Sekolah Asal</div>
                <div class="colon">:</div>
                <div class="value uppercase">{{ $siswa->pelengkap->sekolah_asal ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">11.</div>
                <div class="label">Diterima di sekolah ini</div>
                <div class="colon">:</div>
                <div class="value"></div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">Di kelas</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->diterima_kelas ?? 'X' }}</div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">Pada tanggal</div>
                <div class="colon">:</div>
                <div class="value">14 Juli 2025</div>
            </div>
            
            <div class="row">
                <div class="no">12.</div>
                <div class="label">Nama Orang Tua</div>
                <div class="colon">:</div>
                <div class="value"></div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">a. Ayah</div>
                <div class="colon">:</div>
                <div class="value capitalize">{{ $siswa->nm_ayah ?? '' }}</div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">b. Ibu</div>
                <div class="colon">:</div>
                <div class="value capitalize">{{ $siswa->nm_ibu ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">13.</div>
                <div class="label">Alamat Orang Tua</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->alamat_ortu ?? '' }}</div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">Nomor Telepon Rumah</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->telepon_ortu ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">14.</div>
                <div class="label">Pekerjaan Orang Tua :</div>
                <div class="colon"></div>
                <div class="value"></div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">a. Ayah</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pekerjaan_ayah ?? '' }}</div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">b. Ibu</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pekerjaan_ibu ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">15.</div>
                <div class="label">Nama Wali Siswa</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->nm_wali ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">16.</div>
                <div class="label">Alamat Wali Peserta Didik</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->alamat_wali ?? '' }}</div>
            </div>
            
            <div class="row sub-item">
                <div class="no"></div>
                <div class="label">Nomor Telepon Rumah</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pelengkap->telepon_wali ?? '' }}</div>
            </div>
            
            <div class="row">
                <div class="no">17.</div>
                <div class="label">Pekerjaan Wali Peserta Didik</div>
                <div class="colon">:</div>
                <div class="value">{{ $siswa->pekerjaan_wali ?? '' }}</div>
            </div>
        </div>
        
        <div class="signature-section">
            <div class="photo-section">
                <div class="photo-placeholder">
                    Foto 3x4
                </div>
            </div>
            
            <div class="signature">
                <div>Bantarujeg, 14 Juli 2025</div>
                <div>Kepala Sekolah</div>
                <div class="signature-name">{{ $kepala_sekolah['nama'] }}</div>
                <div>NIP. {{ $kepala_sekolah['nip'] }}</div>
            </div>
        </div>
    @endforeach
</body>
</html>