<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_siswa';

    protected $primaryKey = 'id_siswa';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'asal_sekolah',
        'alamat_domisili',
        'no_whatsapp',
        'nama_bapak',
        'nama_ibu',
        'no_wa_ayah',
        'no_wa_ibu',
        'pekerjaan_bapak',
        'pekerjaan_ibu',
        'alamat_ortu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_siswa', 'id_siswa');
    }

    public function latestPendaftaran()
    {
        return $this->hasOne(Pendaftaran::class, 'id_siswa', 'id_siswa')->latestOfMany('no_registrasi');
    }
}
