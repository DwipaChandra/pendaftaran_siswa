<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiPanitia extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_panitia';

    protected $primaryKey = 'id_verifikasi';

    public $timestamps = false;

    protected $fillable = [
        'no_registrasi',
        'id_panitia',
        'tgl_verifikasi',
        'catatan_verifikator',
        'status_akhir',
    ];

    protected $casts = [
        'tgl_verifikasi' => 'datetime',
    ];

    public function setNoRegistrasiAttribute($value): void
    {
        $this->attributes['no_registrasi'] = is_numeric($value) ? str_pad((string) ((int) $value), 3, '0', STR_PAD_LEFT) : $value;
    }

    public function getNoRegistrasiAttribute($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_numeric($value) ? str_pad((string) ((int) $value), 3, '0', STR_PAD_LEFT) : (string) $value;
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'no_registrasi', 'no_registrasi');
    }

    public function panitia()
    {
        return $this->belongsTo(Panitia::class, 'id_panitia', 'id_panitia');
    }
}
