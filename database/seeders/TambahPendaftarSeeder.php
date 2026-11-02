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

class TambahPendaftarSeeder extends Seeder
{
    /**
     * Tambah 5 pendaftar baru ke database.
     */
    public function run(): void
    {
        $panitia = Panitia::first();
        $idPanitia = $panitia?->id_panitia ?? 1;

        $jurusans = Jurusan::pluck('id_jurusan')->all();
        $jalurs = JalurPendaftaran::pluck('id_jalur')->all();
        $gelombangs = GelombangPendaftaran::pluck('id_gelombang')->all();

        if (empty($jurusans) || empty($jalurs) || empty($gelombangs)) {
            $this->command?->error('Master data jurusan, jalur, atau gelombang kosong.');

            return;
        }

        $dummyStudents = [
            [
                'nama_lengkap' => 'I Wayan Adi Saputra',
                'nisn' => '0091234001',
                'tempat_lahir' => 'Gianyar',
                'tanggal_lahir' => '2009-04-12',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Gianyar',
                'alamat_domisili' => 'Jl. Astina Utara No. 7, Gianyar',
                'no_whatsapp' => '081338220001',
                'nama_bapak' => 'I Ketut Saputra',
                'nama_ibu' => 'Ni Wayan Sukerti',
                'no_wa_ayah' => '081338220011',
                'no_wa_ibu' => '081338220021',
                'pekerjaan_bapak' => 'Pengrajin',
                'pekerjaan_ibu' => 'Pedagang',
                'alamat_ortu' => 'Jl. Astina Utara No. 7, Gianyar',
                'jurusan_index' => 0,
                'jalur_index' => 1,
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Putu Ayu Candra Kirana',
                'nisn' => '0091234002',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '2009-12-01',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 5 Denpasar',
                'alamat_domisili' => 'Jl. Tukad Badung No. 33, Denpasar Selatan',
                'no_whatsapp' => '081338220002',
                'nama_bapak' => 'I Made Wirawan',
                'nama_ibu' => 'Ni Ketut Candri',
                'no_wa_ayah' => '081338220012',
                'no_wa_ibu' => '081338220022',
                'pekerjaan_bapak' => 'Polri',
                'pekerjaan_ibu' => 'Bidan',
                'alamat_ortu' => 'Jl. Tukad Badung No. 33, Denpasar Selatan',
                'jurusan_index' => 1,
                'jalur_index' => 0,
                'gelombang_index' => 0,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Menunggu',
            ],
            [
                'nama_lengkap' => 'Kadek Rizky Firmansyah',
                'nisn' => '0091234003',
                'tempat_lahir' => 'Tabanan',
                'tanggal_lahir' => '2009-07-18',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'asal_sekolah' => 'SMP Negeri 2 Tabanan',
                'alamat_domisili' => 'Jl. Gajah Mada No. 20, Tabanan',
                'no_whatsapp' => '081338220003',
                'nama_bapak' => 'Ahmad Firmansyah',
                'nama_ibu' => 'Ni Luh Mega',
                'no_wa_ayah' => '081338220013',
                'no_wa_ibu' => '081338220023',
                'pekerjaan_bapak' => 'Karyawan Swasta',
                'pekerjaan_ibu' => 'Guru',
                'alamat_ortu' => 'Jl. Gajah Mada No. 20, Tabanan',
                'jurusan_index' => 3,
                'jalur_index' => 1,
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Perbaikan Berkas',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'Ni Made Devi Anggraeni',
                'nisn' => '0091234004',
                'tempat_lahir' => 'Klungkung',
                'tanggal_lahir' => '2009-09-25',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Semarapura',
                'alamat_domisili' => 'Jl. Puputan No. 15, Semarapura, Klungkung',
                'no_whatsapp' => '081338220004',
                'nama_bapak' => 'I Nyoman Sudewa',
                'nama_ibu' => 'Ni Ketut Arini',
                'no_wa_ayah' => '081338220014',
                'no_wa_ibu' => '081338220024',
                'pekerjaan_bapak' => 'PNS',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'alamat_ortu' => 'Jl. Puputan No. 15, Semarapura, Klungkung',
                'jurusan_index' => 2,
                'jalur_index' => 2,
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Menunggu Verifikasi',
                'daftar_ulang' => null,
            ],
            [
                'nama_lengkap' => 'I Gede Arya Dharma Putra',
                'nisn' => '0091234005',
                'tempat_lahir' => 'Karangasem',
                'tanggal_lahir' => '2008-11-05',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'asal_sekolah' => 'SMP Negeri 1 Amlapura',
                'alamat_domisili' => 'Jl. Sultan Agung No. 9, Amlapura, Karangasem',
                'no_whatsapp' => '081338220005',
                'nama_bapak' => 'I Wayan Dharma',
                'nama_ibu' => 'Ni Made Suci',
                'no_wa_ayah' => '081338220015',
                'no_wa_ibu' => '081338220025',
                'pekerjaan_bapak' => 'Petani',
                'pekerjaan_ibu' => 'Pedagang',
                'alamat_ortu' => 'Jl. Sultan Agung No. 9, Amlapura, Karangasem',
                'jurusan_index' => 0,
                'jalur_index' => 3,
                'gelombang_index' => 1,
                'tgl_daftar' => '2026-10-22',
                'status_berkas' => 'Diterima',
                'daftar_ulang' => 'Sudah Daftar Ulang',
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

                // 1. Akun User Siswa
                $email = 'siswa_'.$noReg.'@smktibaliglobalbadung.sch.id';
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'password' => Hash::make('siswa123'),
                        'role' => 'siswa',
                    ]
                );

                // 2. Data Profil Calon Siswa
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

                // 3. Data Pendaftaran
                $idJurusan = $jurusans[$data['jurusan_index'] % count($jurusans)];
                $idJalur = $jalurs[$data['jalur_index'] % count($jalurs)];
                $idGelombang = $gelombangs[$data['gelombang_index'] % count($gelombangs)];

                $pendaftaran = Pendaftaran::create([
                    'no_registrasi' => $noReg,
                    'id_siswa' => $calonSiswa->id_siswa,
                    'id_jurusan' => $idJurusan,
                    'id_jalur' => $idJalur,
                    'id_gelombang' => $idGelombang,
                    'tgl_daftar' => $data['tgl_daftar'],
                    'status_berkas' => $data['status_berkas'],
                ]);

                // 4. Berkas Persyaratan
                $berkasList = [
                    ['jenis_berkas' => 'Pasfoto Formal', 'nama_file' => 'pasfoto_'.$noReg.'.jpg', 'ukuran_file' => 324500, 'path_file' => 'berkas/pasfoto_'.$noReg.'.jpg'],
                    ['jenis_berkas' => 'Kartu Keluarga (KK)', 'nama_file' => 'kk_'.$noReg.'.pdf', 'ukuran_file' => 648200, 'path_file' => 'berkas/kk_'.$noReg.'.pdf'],
                    ['jenis_berkas' => 'Akta Kelahiran Asli', 'nama_file' => 'akta_'.$noReg.'.pdf', 'ukuran_file' => 512400, 'path_file' => 'berkas/akta_'.$noReg.'.pdf'],
                    ['jenis_berkas' => 'Rapor / Nilai', 'nama_file' => 'rapor_'.$noReg.'.pdf', 'ukuran_file' => 1024000, 'path_file' => 'berkas/rapor_'.$noReg.'.pdf'],
                ];
                foreach ($berkasList as $b) {
                    BerkasPersyaratan::create([
                        'no_registrasi' => $pendaftaran->no_registrasi,
                        'jenis_berkas' => $b['jenis_berkas'],
                        'nama_file' => $b['nama_file'],
                        'ukuran_file' => $b['ukuran_file'],
                        'path_file' => $b['path_file'],
                    ]);
                }

                // 5. Verifikasi Panitia jika berkas sudah diproses
                if ($data['status_berkas'] === 'Diterima') {
                    VerifikasiPanitia::create([
                        'no_registrasi' => $pendaftaran->no_registrasi,
                        'id_panitia' => $idPanitia,
                        'tgl_verifikasi' => $data['tgl_daftar'].' 11:30:00',
                        'catatan_verifikator' => 'Berkas pendaftaran lengkap, valid, dan memenuhi seluruh kriteria persyaratan.',
                        'status_akhir' => 'Diterima',
                    ]);

                    if ($data['daftar_ulang']) {
                        Cache::put('daftar_ulang_status_'.$noReg, $data['daftar_ulang'], now()->addMonths(6));
                        if ($data['daftar_ulang'] === 'Sudah Daftar Ulang') {
                            Cache::put('daftar_ulang_date_'.$noReg, date('d M Y', strtotime($data['tgl_daftar'])).' 14:15 WITA', now()->addMonths(6));
                        }
                    }
                } elseif ($data['status_berkas'] === 'Perbaikan Berkas') {
                    VerifikasiPanitia::create([
                        'no_registrasi' => $pendaftaran->no_registrasi,
                        'id_panitia' => $idPanitia,
                        'tgl_verifikasi' => $data['tgl_daftar'].' 13:20:00',
                        'catatan_verifikator' => 'Hasil scan dokumen Kartu Keluarga terpotong, silakan unggah ulang salinan KK yang utuh dan jelas.',
                        'status_akhir' => 'Perbaikan Berkas',
                    ]);
                }

                // 6. Log Aktivitas Admin
                AdminActivityLog::record(
                    actionType: 'pendaftaran_baru',
                    title: 'Pendaftaran Siswa Baru #'.$noReg,
                    description: $data['nama_lengkap'].' terdaftar pada jurusan '.Jurusan::find($idJurusan)?->nama_jurusan,
                    noRegistrasi: $noReg,
                    badgeLabel: $data['status_berkas'],
                    badgeColor: $data['status_berkas'] === 'Diterima' ? 'emerald' : ($data['status_berkas'] === 'Perbaikan Berkas' ? 'amber' : 'blue'),
                    icon: 'user-plus',
                    adminName: 'Sistem SPMB'
                );
            }

            DB::commit();
            $this->command?->info('Berhasil menambahkan 5 pendaftar baru ke database.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('Gagal menambahkan data: '.$e->getMessage());
            throw $e;
        }
    }
}
