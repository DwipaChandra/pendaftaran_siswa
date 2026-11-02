<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $primaryKey = 'id_jurusan';

    public $timestamps = false;

    protected $fillable = [
        'nama_jurusan',
        'kuota',
    ];

    protected static function booted(): void
    {
        static::saving(function (Jurusan $jurusan) {
            $name = trim((string) $jurusan->nama_jurusan);
            if ($name === 'Tata Kecantikan' || $name === 'TKC') {
                $jurusan->nama_jurusan = 'TKC (Tata Kecantikan)';
            }
        });
    }

    public function getKodeAttribute(): string
    {
        $name = (string) $this->nama_jurusan;
        if (str_contains(strtoupper($name), 'KECANTIKAN')) {
            return 'TKC';
        }

        $code = trim(explode(' ', $name)[0] ?? '', ' ()');

        return strtoupper($code);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_jurusan', 'id_jurusan');
    }
}
