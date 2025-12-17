<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'tabel_kurikulum';
    protected $primaryKey = 'kurikulum_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kurikulum_id',
        'nama_kurikulum',
        'mulai_berlaku',
        'sistem_sks',
        'total_sks',
        'jenjang_pendidikan_id',
        'jurusan_id',
        'create_date',
        'last_update',
        'expired_date',
        'last_sync'
    ];

    protected $casts = [
        'mulai_berlaku' => 'date',
        'create_date' => 'datetime',
        'last_update' => 'datetime',
        'expired_date' => 'datetime',
        'last_sync' => 'datetime',
    ];

    // Relationships
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kurikulum_id', 'kurikulum_id');
    }
}