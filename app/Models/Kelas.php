<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'tabel_kelas';
    protected $primaryKey = 'rombongan_belajar_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'rombongan_belajar_id',
        'sekolah_id',
        'semester_id',
        'jurusan_id',
        'ptk_id',
        'nm_kelas',
        'tingkat_pendidikan_id',
        'jenis_rombel',
        'nama_jurusan_sp',
        'jurusan_sp_id',
        'kurikulum_id',
        'program',
        'konsentrasi'
    ];

    // Relationships
    public function waliKelas()
    {
        return $this->belongsTo(Ptk::class, 'ptk_id', 'ptk_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id', 'kurikulum_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id_jurusan');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'tabel_anggotakelas', 'rombongan_belajar_id', 'peserta_didik_id');
    }

    // Accessor for display name
    public function getNamaKelasAttribute()
    {
        return $this->nm_kelas;
    }

    // Get student count (use withCount for better performance)
    public function getJumlahSiswaAttribute()
    {
        // If loaded with withCount, use that, otherwise fallback to query
        return $this->anggota_kelas_count ?? $this->anggotaKelas()->count();
    }

    // Get tingkat display
    public function getTingkatAttribute()
    {
        $tingkatMap = [
            '10' => 'X',
            '11' => 'XI', 
            '12' => 'XII'
        ];
        
        return $tingkatMap[$this->tingkat_pendidikan_id] ?? $this->tingkat_pendidikan_id;
    }
}