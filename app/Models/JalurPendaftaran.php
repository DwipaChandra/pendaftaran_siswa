<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JalurPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'jalur_pendaftaran';

    protected $primaryKey = 'id_jalur';

    public $timestamps = false;

    protected $fillable = [
        'nama_jalur',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_jalur', 'id_jalur');
    }
}
