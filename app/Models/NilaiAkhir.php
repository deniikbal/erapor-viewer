<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $table = 'tabel_nilaiakhir';
    protected $primaryKey = 'id_nilai_akhir';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_nilai_akhir',
        'anggota_rombel_id',
        'mata_pelajaran_id',
        'semester_id',
        'nilai_peng',
        'predikat_peng',
        'nilai_ket',
        'predikat_ket',
        'nilai_sik',
        'predikat_sik',
        'nilai_siksos',
        'predikat_siksos',
        'peserta_didik_id',
        'id_minat',
        'semester'
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id', 'peserta_didik_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
    }

    public function anggotaKelas()
    {
        return $this->belongsTo(AnggotaKelas::class, 'anggota_rombel_id', 'anggota_rombel_id');
    }
}