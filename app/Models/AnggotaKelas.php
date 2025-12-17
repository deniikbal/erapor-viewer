<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaKelas extends Model
{
    use HasFactory;

    protected $table = 'tabel_anggotakelas';
    protected $primaryKey = 'anggota_rombel_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'anggota_rombel_id',
        'peserta_didik_id',
        'rombongan_belajar_id',
        'semester_id'
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id', 'peserta_didik_id');
    }
}