<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GelombangPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'gelombang_pendaftaran';

    protected $primaryKey = 'id_gelombang';

    public $timestamps = false;

    protected $fillable = [
        'nama_gelombang',
        'tgl_buka',
        'tgl_tutup',
        'keterangan_diskon',
    ];

    protected $casts = [
        'tgl_buka' => 'date',
        'tgl_tutup' => 'date',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_gelombang', 'id_gelombang');
    }
}
