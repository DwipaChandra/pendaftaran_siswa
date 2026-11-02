<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranSiswaItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_siswa_item';

    protected $fillable = [
        'pembayaran_id',
        'kategori',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'float',
    ];

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(PembayaranSiswa::class, 'pembayaran_id', 'id');
    }
}
