<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Identitas Siswa</title>
    <style>
        @page {
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 3px 2px;
            vertical-align: top;
        }
        .col-no {
            width: 5%;
        }
        .col-label {
            width: 35%;
        }
        .col-colon {
            width: 2%;
        }
        .col-value {
            width: 58%;
        }
        .sub-label {
            padding-left: 15px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
            position: relative;
            height: 150px;
        }
        .photo-box {
            position: absolute;
            left: 40px;
            top: 0;
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            text-align: center;
            line-height: 4cm;
            font-size: 9pt;
        }
        .signature-box {
            position: absolute;
            right: 0;
            top: 0;
            width: 300px;
            text-align: left;
        }
    </style>
</head>
<body>
    <!-- Header with logos -->
    <div style="text-align: center; margin-bottom: 30px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 20%; text-align: center; vertical-align: middle;">
                    @if(isset($logos['logo_pemda']) && $logos['logo_pemda'])
                        <img src="{{ public_path('storage/' . ltrim($logos['logo_pemda'], '/storage/')) }}" 
                             style="width: 60px; height: auto; max-height: 80px;" alt="Logo Pemda">
                    @endif
                </td>
                <td style="width: 60%; text-align: center; vertical-align: middle;">
                    <div style="font-weight: bold; font-size: 16pt; line-height: 1.2;">
                        SEKOLAH MENENGAH ATAS hhhh<br>
                        NEGERI 1 BANTARUJEG<br>
                        <span style="font-size: 12pt;">Jl. Siliwangi No. 119 Bantarujeg</span>
                    </div>
                </td>
                <td style="width: 20%; text-align: center; vertical-align: middle;">
                    @if(isset($logos['logo_sekolah']) && $logos['logo_sekolah'])
                        <img src="{{ public_path('storage/' . ltrim($logos['logo_sekolah'], '/storage/')) }}" 
                             style="width: 60px; height: auto; max-height: 80px;" alt="Logo Sekolah">
                    @endif
                </td>
            </tr>
        </table>
        <hr style="border: 1px solid #000; margin: 15px 0;">
        <div style="font-weight: bold; font-size: 14pt; margin-top: 15px;">
            IDENTITAS PESERTA DIDIK
        </div>
    </div>

    <table>
        <tr>
            <td class="col-no">1.</td>
            <td class="col-label">Nama Lengkap Peserta Didik</td>
            <td class="col-colon">:</td>
            <td class="col-value"><strong>{{ $siswa['full_name'] }}</strong></td>
        </tr>
        <tr>
            <td class="col-no">2.</td>
            <td class="col-label">Nomor Induk/NISN</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['nis'] }} / {{ $siswa['nisn'] }}</td>
        </tr>
        <tr>
            <td class="col-no">3.</td>
            <td class="col-label">Tempat, Tanggal Lahir</td>
            <td class="col-colon">:</td>
            <td class="col-value">
                {{ $siswa['birth_place'] }}, 
                @if($siswa['birth_date'])
                    {{ \Carbon\Carbon::parse($siswa['birth_date'])->translatedFormat('d F Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="col-no">4.</td>
            <td class="col-label">Jenis Kelamin</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['gender'] }}</td>
        </tr>
        <tr>
            <td class="col-no">5.</td>
            <td class="col-label">Agama</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['religion'] }}</td>
        </tr>
        <tr>
            <td class="col-no">6.</td>
            <td class="col-label">Status dalam Keluarga</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['family_status'] }}</td>
        </tr>
        <tr>
            <td class="col-no">7.</td>
            <td class="col-label">Anak ke</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['child_number'] }}</td>
        </tr>
        <tr>
            <td class="col-no">8.</td>
            <td class="col-label">Alamat Peserta Didik</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['address'] }}</td>
        </tr>
        <tr>
            <td class="col-no">9.</td>
            <td class="col-label">Nomor Telepon Rumah</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['phone_number'] }}</td>
        </tr>
        <tr>
            <td class="col-no">10.</td>
            <td class="col-label">Sekolah Asal</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ strtoupper($siswa['previous_school']) }}</td>
        </tr>
        <tr>
            <td class="col-no">11.</td>
            <td class="col-label">Diterima di sekolah ini</td>
            <td class="col-colon"></td>
            <td class="col-value"></td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">Di kelas</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['accepted_class'] }}</td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">Pada tanggal</td>
            <td class="col-colon">:</td>
            <td class="col-value">14 Juli 2025</td>
        </tr>
        <tr>
            <td class="col-no">12.</td>
            <td class="col-label">Nama Orang Tua</td>
            <td class="col-colon"></td>
            <td class="col-value"></td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">a. Ayah</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ ucwords(strtolower($siswa['father_name'])) }}</td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">b. Ibu</td>
            <td class="colon">:</td>
            <td class="col-value">{{ ucwords(strtolower($siswa['mother_name'])) }}</td>
        </tr>
        <tr>
            <td class="col-no">13.</td>
            <td class="col-label">Alamat Orang Tua</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['parent_address'] }}</td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">Nomor Telepon Rumah</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['parent_phone'] }}</td>
        </tr>
        <tr>
            <td class="col-no">14.</td>
            <td class="col-label">Pekerjaan Orang Tua</td>
            <td class="col-colon"></td>
            <td class="col-value"></td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">a. Ayah</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['father_job'] }}</td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">b. Ibu</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['mother_job'] }}</td>
        </tr>
        <tr>
            <td class="col-no">15.</td>
            <td class="col-label">Nama Wali Siswa</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['guardian_name'] }}</td>
        </tr>
        <tr>
            <td class="col-no">16.</td>
            <td class="col-label">Alamat Wali Peserta Didik</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['guardian_address'] }}</td>
        </tr>
        <tr>
            <td class="col-no"></td>
            <td class="col-label sub-label">Nomor Telepon Rumah</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['guardian_phone'] }}</td>
        </tr>
        <tr>
            <td class="col-no">17.</td>
            <td class="col-label">Pekerjaan Wali Peserta Didik</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $siswa['guardian_job'] }}</td>
        </tr>
    </table>

    <div class="signature-section">
        <div class="photo-box">
            Foto 3x4
        </div>
        
        <div class="signature-box">
            Bantarujeg, 14 Juli 2025<br>
            Kepala Sekolah<br>
            <br><br><br><br>
            <strong><u>{{ $kepala_sekolah['nama'] }}</u></strong><br>
            NIP. {{ $kepala_sekolah['nip'] }}
        </div>
    </div>
</body>
</html>