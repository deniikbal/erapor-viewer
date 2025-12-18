<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'tabel_mapel';
    protected $primaryKey = 'mata_pelajaran_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'mata_pelajaran_id',
        'nm_mapel',
        'kelompok',
        'semester',
        'jurusan_id',
        'urut_rapor',
        'nm_lokal',
        'nm_ringkas'
    ];

    // Relationships
    public function pembelajaran()
    {
        return $this->hasMany(Pembelajaran::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
    }

    public function nilaiAkhir()
    {
        return $this->hasMany(NilaiAkhir::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
    }

    // Accessor for compatibility
    public function getNamaMapelAttribute()
    {
        return $this->nm_mapel;
    }
}