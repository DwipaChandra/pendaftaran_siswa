<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\BerkasPersyaratan;
use App\Models\CalonSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyPendaftarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $panitia = Panitia::first();
        $idPanitia = $panitia?->id_panitia ?? 1;

        $jurusans = Jurusan::pluck('id_jurusan')->all();
        $jalurs = JalurPendaftaran::pluck('id_jalur')->all();
        $gelombangs = GelombangPendaftaran::pluck('id_gelombang')->all();

        if (empty($jurusans) || empty($jalurs) || empty($gelombangs)) {
            $this->command?->error('Master data jurusan, jalur, atau gelombang kosong. Jalankan DatabaseSeeder terlebih dahulu.');

            return;
        }

        $dummyStudents = [
            [
                'nama_lengkap' => 'I Putu Gede Mahardika',
                'nisn' => '0081234561',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '2009-05-15',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Denpasar',
                'alamat_domisili' => 'Jl. Melati No. 12, Dangin Puri Kangin, Denpasar Timur',
                'no_whatsapp' => '081238910001',
                'nama_bapak' => 'I Wayan Mahardika',
                'nama_ibu' => 'Ni Made Ratna',
                'no_wa_ayah' => '081238910011',
                'no_wa_ibu' => '081238910021',
                'pekerjaan_bapak' => 'PNS',
                'pekerjaan_ibu' => 'Wiraswasta',
                'alamat_ortu' => 'Jl. Melati No. 12, Dangin Puri Kangin, Denpasar Timur',
                'jurusan_index' => 0, // PPLG
                'jalur_index' => 0,   // Prestasi
                'gelombang_index' => 1, // Gelombang 1
                'tgl_daftar' => '2026-10-14',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Sudah Daftar Ulang',
            ],
            [
                'nama_lengkap' => 'Ni Made Ayu Wandira',
                'nisn' => '0082345672',
                'tempat_lahir' => 'Badung',
                'tanggal_lahir' => '2009-08-22',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 2 Kuta Utara',
                'alamat_domisili' => 'Jl. Raya Dalung No. 45, Kuta Utara, Badung',
                'no_whatsapp' => '081238910002',
                'nama_bapak' => 'I Made Suarta',
                'nama_ibu' => 'Ni Ketut Sari',
                'no_wa_ayah' => '081238910012',
                'no_wa_ibu' => '081238910022',
                'pekerjaan_bapak' => 'Karyawan Swasta',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'alamat_ortu' => 'Jl. Raya Dalung No. 45, Kuta Utara, Badung',
                'jurusan_index' => 1, // DKV
                'jalur_index' => 1,   // Umum
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-15',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Menunggu',
            ],
            [
                'nama_lengkap' => 'I Komang Raditya Pratama',
                'nisn' => '0083456783',
                'tempat_lahir' => 'Tabanan',
                'tanggal_lahir' => '2009-03-10',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Mengwi',
                'alamat_domisili' => 'Jl. Raya Kapal No. 8, Mengwi, Badung',
                'no_whatsapp' => '081238910003',
                'nama_bapak' => 'I Ketut Pratama',
                'nama_ibu' => 'Ni Luh Putu Eka',
                'no_wa_ayah' => '081238910013',
                'no_wa_ibu' => '081238910023',
                'pekerjaan_bapak' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Pedagang',
                'alamat_ortu' => 'Jl. Raya Kapal No. 8, Mengwi, Badung',
                'jurusan_index' => 3, // TJKT
                'jalur_index' => 1,   // Umum
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-16',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Ni Ketut Sri Wahyuni',
                'nisn' => '0084567894',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '2009-07-04',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 3 Denpasar',
                'alamat_domisili' => 'Jl. Pulau Kawe No. 25, Denpasar Selatan',
                'no_whatsapp' => '081238910004',
                'nama_bapak' => 'I Wayan Sudarma',
                'nama_ibu' => 'Ni Made Artini',
                'no_wa_ayah' => '081238910014',
                'no_wa_ibu' => '081238910024',
                'pekerjaan_bapak' => 'Karyawan Swasta',
                'pekerjaan_ibu' => 'PNS',
                'alamat_ortu' => 'Jl. Pulau Kawe No. 25, Denpasar Selatan',
                'jurusan_index' => 2, // BD
                'jalur_index' => 3,   // KIP
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-17',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Ahmad Fajar Alamsyah',
                'nisn' => '0085678905',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '2008-11-19',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'asal_sekolah' => 'MTs Negeri 1 Denpasar',
                'alamat_domisili' => 'Jl. Teuku Umar Gg. Rajawali No. 10, Denpasar',
                'no_whatsapp' => '081238910005',
                'nama_bapak' => 'Bambang Alamsyah',
                'nama_ibu' => 'Siti Aminah',
                'no_wa_ayah' => '081238910015',
                'no_wa_ibu' => '081238910025',
                'pekerjaan_bapak' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'alamat_ortu' => 'Jl. Teuku Umar Gg. Rajawali No. 10, Denpasar',
                'jurusan_index' => 0, // PPLG
                'jalur_index' => 0,   // Prestasi
                'gelombang_index' => 0, // Gelombang Khusus
                'tgl_daftar' => '2026-10-18',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Sudah Daftar Ulang',
            ],
            [
                'nama_lengkap' => 'Kadek Dwi Lestari',
                'nisn' => '0086789016',
                'tempat_lahir' => 'Badung',
                'tanggal_lahir' => '2009-01-08',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Kuta',
                'alamat_domisili' => 'Jl. Bakung Sari No. 17, Kuta, Badung',
                'no_whatsapp' => '081238910006',
                'nama_bapak' => 'I Made Sudiartha',
                'nama_ibu' => 'Ni Nyoman Trisna',
                'no_wa_ayah' => '081238910016',
                'no_wa_ibu' => '081238910026',
                'pekerjaan_bapak' => 'Karyawan Hotel',
                'pekerjaan_ibu' => 'Staf Restoran',
                'alamat_ortu' => 'Jl. Bakung Sari No. 17, Kuta, Badung',
                'jurusan_index' => 4, // TKC
                'jalur_index' => 4,   // Khusus
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-19',
                'status_berkas' => 'Perbaikan Berkas',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Michael Christian Tan',
                'nisn' => '0087890127',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '2009-09-30',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Kristen',
                'asal_sekolah' => 'SMP Kristen Harapan Denpasar',
                'alamat_domisili' => 'Jl. Sunset Road No. 88, Kuta, Badung',
                'no_whatsapp' => '081238910007',
                'nama_bapak' => 'David Tan',
                'nama_ibu' => 'Maria Kristina',
                'no_wa_ayah' => '081238910017',
                'no_wa_ibu' => '081238910027',
                'pekerjaan_bapak' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Akuntan',
                'alamat_ortu' => 'Jl. Sunset Road No. 88, Kuta, Badung',
                'jurusan_index' => 1, // DKV
                'jalur_index' => 1,   // Umum
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-20',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Ni Luh Putu Sintya Dewi',
                'nisn' => '0088901238',
                'tempat_lahir' => 'Badung',
                'tanggal_lahir' => '2009-02-14',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 4 Kuta Utara',
                'alamat_domisili' => 'Jl. Padang Luwih No. 50, Dalung, Badung',
                'no_whatsapp' => '081238910008',
                'nama_bapak' => 'I Wayan Sukadana',
                'nama_ibu' => 'Ni Luh Murni',
                'no_wa_ayah' => '081238910018',
                'no_wa_ibu' => '081238910028',
                'pekerjaan_bapak' => 'Guru',
                'pekerjaan_ibu' => 'Perawat',
                'alamat_ortu' => 'Jl. Padang Luwih No. 50, Dalung, Badung',
                'jurusan_index' => 3, // TJKT
                'jalur_index' => 2,   // Afirmasi
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-21',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Menunggu',
            ],
            [
                'nama_lengkap' => 'Gede Bayu Nugraha',
                'nisn' => '0089012349',
                'tempat_lahir' => 'Singaraja',
                'tanggal_lahir' => '2009-06-03',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Singaraja',
                'alamat_domisili' => 'Jl. Gajah Mada No. 15, Banjar Jawa, Buleleng',
                'no_whatsapp' => '081238910009',
                'nama_bapak' => 'I Nyoman Nugraha',
                'nama_ibu' => 'Ni Ketut Suartini',
                'no_wa_ayah' => '081238910019',
                'no_wa_ibu' => '081238910029',
                'pekerjaan_bapak' => 'TNI',
                'pekerjaan_ibu' => 'PNS',
                'alamat_ortu' => 'Jl. Gajah Mada No. 15, Banjar Jawa, Buleleng',
                'jurusan_index' => 0, // PPLG
                'jalur_index' => 0,   // Prestasi
                'gelombang_index' => 0, // Gelombang Khusus
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Ditolak',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Ni Nyoman Tri Hapsari',
                'nisn' => '0080123450',
                'tempat_lahir' => 'Badung',
                'tanggal_lahir' => '2008-10-27',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Kuta Selatan',
                'alamat_domisili' => 'Jl. Kampus Unud No. 3, Jimbaran, Kuta Selatan',
                'no_whatsapp' => '081238910010',
                'nama_bapak' => 'I Wayan Hapsara',
                'nama_ibu' => 'Ni Made Dewi',
                'no_wa_ayah' => '081238910020',
                'no_wa_ibu' => '081238910030',
                'pekerjaan_bapak' => 'Dosen',
                'pekerjaan_ibu' => 'Dokter',
                'alamat_ortu' => 'Jl. Kampus Unud No. 3, Jimbaran, Kuta Selatan',
                'jurusan_index' => 2, // BD
                'jalur_index' => 1,   // Umum
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
        ];

        DB::beginTransaction();
        try {
            $lastNumber = (int) (Pendaftaran::whereRaw("no_registrasi REGEXP '^[0-9]+$'")
                ->selectRaw('MAX(CAST(no_registrasi AS UNSIGNED)) as max_num')
                ->value('max_num') ?? 0);

            foreach ($dummyStudents as $data) {
                $lastNumber++;
                $noReg = str_pad((string) $lastNumber, 3, '0', STR_PAD_LEFT);

                // 1. Reuse the student profile if this seed data already exists.
                $calonSiswa = CalonSiswa::where('nisn', $data['nisn'])->first();
                if (! $calonSiswa) {
                    $email = 'siswa_'.$noReg.'@smktibaliglobalbadung.sch.id';
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'password' => Hash::make('siswa123'),
                            'role' => 'siswa',
                        ]
                    );

                    $calonSiswa = CalonSiswa::create([
                        'id_user' => $user->id_user,
                        'nama_lengkap' => $data['nama_lengkap'],
                        'nisn' => $data['nisn'],
                        'tempat_lahir' => $data['tempat_lahir'],
                        'tanggal_lahir' => $data['tanggal_lahir'],
                        'jenis_kelamin' => $data['jenis_kelamin'],
                        'agama' => $data['agama'],
                        'asal_sekolah' => $data['asal_sekolah'],
                        'alamat_domisili' => $data['alamat_domisili'],
                        'no_whatsapp' => $data['no_whatsapp'],
                        'nama_bapak' => $data['nama_bapak'],
                        'nama_ibu' => $data['nama_ibu'],
                        'no_wa_ayah' => $data['no_wa_ayah'],
                        'no_wa_ibu' => $data['no_wa_ibu'],
                        'pekerjaan_bapak' => $data['pekerjaan_bapak'],
                        'pekerjaan_ibu' => $data['pekerjaan_ibu'],
                        'alamat_ortu' => $data['alamat_ortu'],
                    ]);
                }

                // 3. Data Pendaftaran
                $idJurusan = $jurusans[$data['jurusan_index'] % count($jurusans)];
                $idJalur = $jalurs[$data['jalur_index'] % count($jalurs)];
                $idGelombang = $gelombangs[$data['gelombang_index'] % count($gelombangs)];

                $pendaftaran = Pendaftaran::firstOrCreate(
                    ['id_siswa' => $calonSiswa->id_siswa],
                    [
                        'no_registrasi' => $noReg,
                        'id_jurusan' => $idJurusan,
                        'id_jalur' => $idJalur,
                        'id_gelombang' => $idGelombang,
                        'tgl_daftar' => $data['tgl_daftar'],
                        'status_berkas' => $data['status_berkas'],
                    ]
                );
                $noReg = $pendaftaran->no_registrasi;

                // 4. Berkas Persyaratan
                $berkasList = [
                    ['jenis_berkas' => 'Pasfoto Formal', 'nama_file' => 'pasfoto.png', 'ukuran_file' => 1869033, 'path_file' => 'assets/img/berkas/pasfoto.png'],
                    ['jenis_berkas' => 'Kartu Keluarga (KK)', 'nama_file' => 'kk.jpg', 'ukuran_file' => 175509, 'path_file' => 'assets/img/berkas/kk.jpg'],
                    ['jenis_berkas' => 'Akta Kelahiran Asli', 'nama_file' => 'akta.png', 'ukuran_file' => 889616, 'path_file' => 'assets/img/berkas/akta.png'],
                ];
                foreach ($berkasList as $b) {
                    BerkasPersyaratan::firstOrCreate(
                        [
                            'no_registrasi' => $pendaftaran->no_registrasi,
                            'jenis_berkas' => $b['jenis_berkas'],
                        ],
                        [
                            'nama_file' => $b['nama_file'],
                            'ukuran_file' => $b['ukuran_file'],
                            'path_file' => $b['path_file'],
                        ]
                    );
                }

                // 5. Verifikasi Panitia jika berkas sudah diproses
                if ($data['status_berkas'] === 'Diterima') {
                    VerifikasiPanitia::firstOrCreate(
                        ['no_registrasi' => $pendaftaran->no_registrasi],
                        [
                            'id_panitia' => $idPanitia,
                            'tgl_verifikasi' => $data['tgl_daftar'].' 11:30:00',
                            'catatan_verifikator' => 'Berkas pendaftaran lengkap, valid, dan memenuhi seluruh kriteria persyaratan.',
                            'status_akhir' => 'Diterima',
                        ]
                    );

                    if ($data['daftar_ulang']) {
                        Cache::put('daftar_ulang_status_'.$noReg, $data['daftar_ulang'], now()->addMonths(6));
                        if ($data['daftar_ulang'] === 'Sudah Daftar Ulang') {
                            Cache::put('daftar_ulang_date_'.$noReg, date('d M Y', strtotime($data['tgl_daftar'])).' 14:15 WITA', now()->addMonths(6));
                        }
                    }
                } elseif ($data['status_berkas'] === 'Perbaikan Berkas') {
                    VerifikasiPanitia::firstOrCreate(
                        ['no_registrasi' => $pendaftaran->no_registrasi],
                        [
                            'id_panitia' => $idPanitia,
                            'tgl_verifikasi' => $data['tgl_daftar'].' 13:20:00',
                            'catatan_verifikator' => 'Hasil scan dokumen Kartu Keluarga terpotong, silakan unggah ulang salinan KK yang utuh dan jelas.',
                            'status_akhir' => 'Perbaikan Berkas',
                        ]
                    );
                } elseif ($data['status_berkas'] === 'Ditolak') {
                    VerifikasiPanitia::firstOrCreate(
                        ['no_registrasi' => $pendaftaran->no_registrasi],
                        [
                            'id_panitia' => $idPanitia,
                            'tgl_verifikasi' => $data['tgl_daftar'].' 15:00:00',
                            'catatan_verifikator' => 'Sertifikat dan dokumen pendukung jalur prestasi tidak memenuhi ketentuan keabsahan tingkat kabupaten/provinsi.',
                            'status_akhir' => 'Ditolak',
                        ]
                    );
                }

                // 6. Log Aktivitas Admin
                if ($pendaftaran->wasRecentlyCreated) {
                    AdminActivityLog::record(
                        actionType: 'pendaftaran_baru',
                        title: 'Pendaftaran Siswa Baru #'.$noReg,
                        description: $data['nama_lengkap'].' terdaftar pada jurusan '.Jurusan::find($idJurusan)?->nama_jurusan,
                        noRegistrasi: $noReg,
                        badgeLabel: $data['status_berkas'],
                        badgeColor: $data['status_berkas'] === 'Diterima' ? 'emerald' : ($data['status_berkas'] === 'Perbaikan Berkas' ? 'amber' : ($data['status_berkas'] === 'Ditolak' ? 'rose' : 'blue')),
                        icon: 'user-plus',
                        adminName: 'Sistem SPMB'
                    );
                }
            }

            DB::commit();
            $this->command?->info('Berhasil mengisikan 10 data pendaftar acak ke database.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('Gagal mengisikan data: '.$e->getMessage());
            throw $e;
        }
    }
}
