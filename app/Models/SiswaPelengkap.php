<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaPelengkap extends Model
{
    use HasFactory;

    protected $table = 'tabel_siswa_pelengkap';
    protected $primaryKey = 'pelengkap_siswa_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'pelengkap_siswa_id',
        'peserta_didik_id',
        'status_dalam_kel',
        'anak_ke',
        'sekolah_asal',
        'diterima_kelas',
        'alamat_ortu',
        'telepon_ortu',
        'alamat_wali',
        'telepon_wali',
        'foto_siswa',
        'no_ijasahnas',
        'tgl_lulus',
        'no_transkrip'
    ];

    protected $casts = [
        'tgl_lulus' => 'date',
        'anak_ke' => 'integer'
    ];

    // Relationship to Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id', 'peserta_didik_id');
    }
}