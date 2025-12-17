<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'tabel_mapel';
    protected $primaryKey = 'id_mapel';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_mapel',
        'nama_mapel',
        'kelompok_mapel',
        'urutan_mapel',
        'status_mapel'
    ];

    // Relationships
    public function pembelajaran()
    {
        return $this->hasMany(Pembelajaran::class, 'id_mapel', 'id_mapel');
    }
}