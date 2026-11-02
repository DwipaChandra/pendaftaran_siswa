<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasPersyaratan extends Model
{
    use HasFactory;

    protected $table = 'berkas_persyaratan';

    protected $primaryKey = 'id_berkas';

    public $timestamps = false;

    protected $fillable = [
        'no_registrasi',
        'jenis_berkas',
        'nama_file',
        'ukuran_file',
        'path_file',
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
}
