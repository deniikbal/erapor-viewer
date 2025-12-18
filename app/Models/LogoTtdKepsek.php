<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogoTtdKepsek extends Model
{
    protected $table = 'tambah.logo_ttdkepsek';
    protected $primaryKey = 'sekolah_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Usually true/false? Backup didn't show timestamps. 'tinker' output cols also didn't show created_at.

    protected $fillable = [
        'sekolah_id',
        'logo_pemda',
        'logo_sek', // Confirmed name
        'ttd_kepsek',
        'kop_sekolah',
    ];

    public function sekolah()
    {
        // Assuming relationship to Sekolah model if needed
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
}
