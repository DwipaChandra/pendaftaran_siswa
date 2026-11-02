<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $primaryKey = 'no_registrasi';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'no_registrasi',
        'id_siswa',
        'id_jurusan',
        'id_jalur',
        'sub_jalur',
        'nama_sub_jalur',
        'voucher_jenis',
        'voucher_kode',
        'mou_tepat_waktu',
        'id_gelombang',
        'tgl_daftar',
        'status_berkas',
    ];

    protected $casts = [
        'tgl_daftar' => 'date',
        'mou_tepat_waktu' => 'boolean',
    ];

    public function setNoRegistrasiAttribute($value): void
    {
        $this->attributes['no_registrasi'] = is_numeric($value)
            ? str_pad((string) $value, 3, '0', STR_PAD_LEFT)
            : (string) $value;
    }

    public function getNoRegistrasiAttribute($value): string
    {
        return is_numeric($value)
            ? str_pad((string) $value, 3, '0', STR_PAD_LEFT)
            : (string) $value;
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'id_jalur', 'id_jalur');
    }

    public function gelombang()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'id_gelombang', 'id_gelombang');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasPersyaratan::class, 'no_registrasi', 'no_registrasi');
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPanitia::class, 'no_registrasi', 'no_registrasi');
    }

    public function keuangan()
    {
        return $this->hasOne(KeuanganSiswa::class, 'no_registrasi', 'no_registrasi');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranSiswa::class, 'no_registrasi', 'no_registrasi');
    }
}
