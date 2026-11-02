<?php

namespace Database\Seeders;

use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Panitia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Master Jurusan
        $jurusanData = [
            ['nama_jurusan' => 'PPLG (Pengembangan Perangkat Lunak & Gim)', 'kuota' => 72],
            ['nama_jurusan' => 'DKV (Desain Komunikasi Visual)', 'kuota' => 72],
            ['nama_jurusan' => 'BD (Bisnis Digital)', 'kuota' => 36],
            ['nama_jurusan' => 'TJKT (Teknik Jaringan Komputer & Telekomunikasi)', 'kuota' => 72],
            ['nama_jurusan' => 'TKC (Tata Kecantikan)', 'kuota' => 72],
        ];
        foreach ($jurusanData as $j) {
            Jurusan::firstOrCreate(['nama_jurusan' => $j['nama_jurusan']], $j);
        }

        // 2. Master Jalur Pendaftaran
        $jalurData = [
            ['nama_jalur' => 'Jalur Prestasi'],
            ['nama_jalur' => 'Jalur Umum'],
            ['nama_jalur' => 'Jalur Afirmasi'],
            ['nama_jalur' => 'Jalur KIP'],
            ['nama_jalur' => 'Jalur Khusus'],
            ['nama_jalur' => 'Jalur Minat Bakat'],
        ];
        foreach ($jalurData as $jl) {
            JalurPendaftaran::firstOrCreate(['nama_jalur' => $jl['nama_jalur']], $jl);
        }

        // 3. Master Gelombang Pendaftaran
        $gelombangData = [
            [
                'nama_gelombang' => 'Gelombang Khusus',
                'tgl_buka' => '2026-01-01',
                'tgl_tutup' => '2026-03-31',
                'keterangan_diskon' => 'Potongan DPP 50%',
            ],
            [
                'nama_gelombang' => 'Gelombang 1',
                'tgl_buka' => '2026-04-01',
                'tgl_tutup' => '2026-05-31',
                'keterangan_diskon' => 'Potongan DPP 25%',
            ],
            [
                'nama_gelombang' => 'Gelombang 2',
                'tgl_buka' => '2026-06-01',
                'tgl_tutup' => '2026-07-15',
                'keterangan_diskon' => 'Reguler',
            ],
        ];
        foreach ($gelombangData as $g) {
            GelombangPendaftaran::firstOrCreate(['nama_gelombang' => $g['nama_gelombang']], $g);
        }

        // 4. Default Admin / Panitia User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@smktibadung.sch.id'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        Panitia::firstOrCreate(
            ['id_user' => $adminUser->id_user],
            [
                'id_user' => $adminUser->id_user,
                'nama_panitia' => 'Administrator SPMB',
                'nip' => '198701012010011001',
                'no_telepon' => '081234567890',
                'bidang_studi' => 'Panitia SPMB',
            ]
        );
    }
}
