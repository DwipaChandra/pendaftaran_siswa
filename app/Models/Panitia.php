<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panitia extends Model
{
    use HasFactory;

    protected $table = 'panitia';

    protected $primaryKey = 'id_panitia';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_panitia',
        'nip',
        'no_telepon',
        'bidang_studi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiPanitia::class, 'id_panitia', 'id_panitia');
    }
}
