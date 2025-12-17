<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_jurusan',
        'nama_jurusan',
        'kode_jurusan'
    ];

    // Relationships
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_jurusan', 'id_jurusan');
    }
}