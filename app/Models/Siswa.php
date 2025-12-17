<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'tabel_siswa';
    protected $primaryKey = 'peserta_didik_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'peserta_didik_id',
        'nis',
        'nisn',
        'nm_siswa',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat_siswa',
        'telepon_siswa',
        'diterima_tanggal',
        'nm_ayah',
        'nm_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'nm_wali',
        'pekerjaan_wali'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'diterima_tanggal' => 'date',
    ];

    // Relationships
    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'peserta_didik_id', 'peserta_didik_id');
    }

    public function nilai()
    {
        return $this->hasMany(NilaiAkhir::class, 'peserta_didik_id', 'peserta_didik_id');
    }

    public function pelengkap()
    {
        return $this->hasOne(SiswaPelengkap::class, 'peserta_didik_id', 'peserta_didik_id');
    }

    // Get current class (kelas saat ini)
    public function kelasAktif()
    {
        return $this->anggotaKelas()
            ->with('kelas')
            ->whereHas('kelas', function($query) {
                $query->where('jenis_rombel', 1);
            })
            ->first();
    }

    // Accessors for compatibility
    public function getNamaSiswaAttribute()
    {
        return $this->nm_siswa;
    }

    public function getJkAttribute()
    {
        return $this->jenis_kelamin;
    }

    public function getAlamatAttribute()
    {
        return $this->alamat_siswa;
    }

    public function getNoHpAttribute()
    {
        return $this->telepon_siswa;
    }
}