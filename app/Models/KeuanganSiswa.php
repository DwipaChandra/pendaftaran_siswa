<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeuanganSiswa extends Model
{
    use HasFactory;

    protected $table = 'keuangan_siswa';

    protected $fillable = [
        'no_registrasi',
        'diskon_spp_custom',
        'durasi_spp',
        'diskon_dpp_custom',
        'durasi_dpp',
        'diskon_jalur_persen',
        'diskon_jurusan_persen',
        'diskon_pendaftaran_persen',
        'minimal_daftar_ulang',
        'catatan_diskon',
        'status_daftar_ulang',
        'status_baju',
        'is_refunded',
        'refund_amount',
        'refund_date',
        'refund_notes',
    ];

    protected $casts = [
        'diskon_spp_custom' => 'float',
        'diskon_dpp_custom' => 'float',
        'diskon_jalur_persen' => 'float',
        'diskon_jurusan_persen' => 'float',
        'diskon_pendaftaran_persen' => 'float',
        'minimal_daftar_ulang' => 'float',
        'is_refunded' => 'boolean',
        'refund_amount' => 'float',
        'refund_date' => 'datetime',
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

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'no_registrasi', 'no_registrasi');
    }
}
