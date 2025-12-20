<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TanggalRapor extends Model
{
    protected $table = 'tabel_tanggalrapor';
    protected $primaryKey = 'tanggal_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_id',
        'semester_id',
        'tanggal',
        'semester',
        'tempat_ttd',
        'status_kepsek',
        'status_nip_kepsek',
        'status_nip_walas',
        'ttd_validasi'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}