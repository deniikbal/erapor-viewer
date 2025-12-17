<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    use HasFactory;

    protected $table = 'tabel_ptk';
    protected $primaryKey = 'ptk_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ptk_id',
        'nama',
        'nip',
        'jenis_ptk_id',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'nuptk',
        'alamat_jalan',
        'status_keaktifan_id',
        'soft_delete'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationships
    public function pembelajaran()
    {
        return $this->hasMany(Pembelajaran::class, 'ptk_id', 'ptk_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'ptk_id', 'ptk_id');
    }

    public function userLogin()
    {
        return $this->hasOne(UserLogin::class, 'ptk_id', 'ptk_id');
    }

    // Accessors for compatibility
    public function getNamaPtkAttribute()
    {
        return $this->nama;
    }

    public function getJkAttribute()
    {
        return $this->jenis_kelamin;
    }

    public function getAlamatAttribute()
    {
        return $this->alamat_jalan;
    }
}