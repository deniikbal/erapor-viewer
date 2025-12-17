<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelajaran extends Model
{
    use HasFactory;

    protected $table = 'tabel_pembelajaran';
    protected $primaryKey = 'id_pembelajaran';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_pembelajaran',
        'id_ptk',
        'id_mapel',
        'id_kelas',
        'semester',
        'kkm'
    ];

    // Relationships
    public function guru()
    {
        return $this->belongsTo(Ptk::class, 'id_ptk', 'id_ptk');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}