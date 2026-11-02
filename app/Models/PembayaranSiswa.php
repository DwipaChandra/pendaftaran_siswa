<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembayaranSiswa extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_siswa';

    protected $fillable = [
        'no_kuitansi',
        'no_registrasi',
        'tipe_transaksi',
        'total_bayar',
        'metode_pembayaran',
        'tgl_bayar',
        'catatan',
        'nama_petugas',
    ];

    protected $casts = [
        'total_bayar' => 'float',
        'tgl_bayar' => 'date',
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

    public function items(): HasMany
    {
        return $this->hasMany(PembayaranSiswaItem::class, 'pembayaran_id', 'id');
    }
}
