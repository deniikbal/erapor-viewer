<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $table = 'tabel_nilaiakhir';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'id_pembelajaran',
        'nilai_akhir',
        'predikat',
        'semester'
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function pembelajaran()
    {
        return $this->belongsTo(Pembelajaran::class, 'id_pembelajaran', 'id_pembelajaran');
    }
}