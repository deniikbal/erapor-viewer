<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'semester_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'semester_id',
        'tahun_ajaran_id',
        'nama_semester',
        'semester',
        'periode_aktif',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}