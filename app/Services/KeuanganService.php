<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\BerkasPersyaratan;
use App\Models\KeuanganSiswa;
use App\Models\PembayaranSiswa;
use App\Models\PembayaranSiswaItem;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KeuanganService
{
    public const KATEGORI_CONFIG = [
        'pendaftaran' => [
            'id' => 'pendaftaran',
            'nama' => 'Pendaftaran',
            'deskripsi' => 'Biaya formulir & pendaftaran calon siswa baru',
            'is_refundable' => false,
            'is_fixed' => false,
        ],
        'mos' => [
            'id' => 'mos',
            'nama' => 'MOS, Kartu Pelajar, Sampul Raport & Kepramukaan',
            'deskripsi' => 'Paket perlengkapan MPLS & kesiswaan awal masuk',
            'is_refundable' => true,
            'is_fixed' => true,
            'nominal_tetap' => 450000,
        ],
        'asuransi' => [
            'id' => 'asuransi',
            'nama' => 'Asuransi (3 Tahun)',
            'deskripsi' => 'Premi perlindungan asuransi kecelakaan siswa selama masa studi',
            'is_refundable' => true,
            'is_fixed' => true,
            'nominal_tetap' => 100000,
        ],
        'seragam' => [
            'id' => 'seragam',
            'nama' => 'Seragam / Baju',
            'deskripsi' => 'Paket seragam lengkap sekolah (Atribut, Olahraga, Kejuruan)',
            'is_refundable' => false,
            'is_fixed' => true,
            'nominal_tetap' => 1975000,
        ],
        'dpp' => [
            'id' => 'dpp',
            'nama' => 'DPP Semester 1',
            'deskripsi' => 'Dana Pengembangan Pendidikan semester ganjil',
            'is_refundable' => true,
            'is_fixed' => false,
        ],
        'spp' => [
            'id' => 'spp',
            'nama' => 'SPP Bulan Pertama',
            'deskripsi' => 'Sumbangan Pembinaan Pendidikan bulan pertama (Juli)',
            'is_refundable' => true,
            'is_fixed' => false,
        ],
        'osis' => [
            'id' => 'osis',
            'nama' => 'OSIS Bulan Pertama',
            'deskripsi' => 'Iuran Organisasi Siswa Intra Sekolah bulan pertama',
            'is_refundable' => true,
            'is_fixed' => true,
            'nominal_tetap' => 25000,
        ],
    ];

    /**
     * Hitung struktur 7 kategori tagihan untuk 1 siswa
     */
    public function calculateStudentBilling(Pendaftaran $pendaftaran): array
    {
        $keuangan = $pendaftaran->keuangan ?? $this->getOrCreateKeuangan($pendaftaran);

        $jurusanNama = $pendaftaran->jurusan?->nama_jurusan ?? '';
        $isTkc = str_contains(strtoupper($jurusanNama), 'KECANTIKAN') || str_starts_with(strtoupper($jurusanNama), 'TKC');
        $isTjkt = str_starts_with(strtoupper($jurusanNama), 'TJKT') || str_contains(strtoupper($jurusanNama), 'JARINGAN');

        $gelombangNama = $pendaftaran->gelombang?->nama_gelombang ?? 'Gelombang Khusus';
        $jalurNama = $pendaftaran->jalur?->nama_jalur ?? 'Jalur Umum';
        $subJalur = strtolower($pendaftaran->sub_jalur ?? '');
        $voucherJenis = strtolower($pendaftaran->voucher_jenis ?? '');

        // 1. Pendaftaran
        $pendaftaranDasar = 150000;
        $isGelombangKhusus = str_contains(strtolower($gelombangNama), 'khusus');
        $isGelombang1 = str_contains(strtolower($gelombangNama), '1');
        $isGelombang2 = str_contains(strtolower($gelombangNama), '2');

        $hasFreeVoucher = in_array($voucherJenis, ['voucher_alumni', 'voucher_hut', 'voucher_lomba'], true);

        if ($hasFreeVoucher) {
            $regDiscPct = 100;
            $regDiscSource = 'Voucher PPDB';
        } elseif ($isGelombangKhusus) {
            $regDiscPct = 100;
            $regDiscSource = $gelombangNama;
        } elseif ($isGelombang1 || $isGelombang2) {
            $regDiscPct = 50;
            $regDiscSource = $gelombangNama;
        } else {
            $regDiscPct = 0;
            $regDiscSource = 'Tarif Normal';
        }
        $pendaftaranBersih = (int) round($pendaftaranDasar - ($pendaftaranDasar * ($regDiscPct / 100)));

        // 2. MOS
        $mosBersih = 450000;

        // 3. Asuransi
        $asuransiBersih = 100000;

        // 4. Seragam
        $seragamBersih = 1975000;

        // 5. DPP Semester 1
        $dppDasar = $isTkc ? 500000 : 650000;
        $dppDuration = '3 Tahun';

        if (! is_null($keuangan->diskon_dpp_custom)) {
            $dppDiscPct = (float) $keuangan->diskon_dpp_custom;
            $dppDiscSource = 'Kustom Admin';
            $dppDuration = $keuangan->durasi_dpp ?: 'Kebijakan Admin';
        } else {
            $jalurLower = strtolower($jalurNama);
            $isUmum = str_contains($jalurLower, 'umum');
            $isOjol = str_contains($subJalur, 'ojol') || str_contains($jalurLower, 'ojol') || str_contains($jalurLower, 'ojek');
            $isMinatBakat = str_contains($subJalur, 'minat') || str_contains($subJalur, 'bakat') || str_contains($jalurLower, 'minat');
            $isInklusi = str_contains($subJalur, 'inklusi') || str_contains($jalurLower, 'inklusi');

            if ($isInklusi) {
                $dppDiscPct = 0;
                $dppDuration = 'Kebijakan Kepala Sekolah';
                $dppDiscSource = 'Jalur Inklusi (Kebijakan Kepsek)';
            } elseif ($isOjol) {
                $dppDiscPct = 50;
                $dppDuration = '1 Tahun';
                $dppDiscSource = 'Putra/Putri Ojek Online (1 Tahun)';
            } elseif ($isMinatBakat) {
                $dppDiscPct = 50;
                $dppDuration = '1 Tahun';
                $dppDiscSource = 'Jalur Minat & Bakat (1 Tahun)';
            } elseif ($isUmum) {
                if ($isGelombangKhusus) {
                    $dppDiscPct = 50;
                } elseif ($isGelombang1) {
                    $dppDiscPct = 30;
                } elseif ($isGelombang2) {
                    $dppDiscPct = 25;
                } else {
                    $dppDiscPct = 25;
                }
                $dppDuration = '3 Tahun';
                $dppDiscSource = "{$gelombangNama} (3 Tahun)";
            } else {
                // Prestasi, Afirmasi, KIP, Khusus (MOU, Kembar, Saudara, Guru, Panti)
                $dppDiscPct = 50;
                $dppDuration = '3 Tahun';
                $dppDiscSource = "{$jalurNama} (3 Tahun)";
            }
        }
        $dppBersih = (int) round($dppDasar - ($dppDasar * ($dppDiscPct / 100)));

        // 6. SPP Bulan Pertama
        $sppDasar = $isTkc ? 450000 : 650000;
        $jurusanSppBonus = $isTjkt ? 10 : 5;
        $sppDuration = '3 Tahun';

        if (! is_null($keuangan->diskon_spp_custom)) {
            $totalSppDiscPct = (float) $keuangan->diskon_spp_custom;
            $sppDiscSource = 'Kustom Admin (Kebijakan Kepala Sekolah)';
            $sppDuration = $keuangan->durasi_spp ?: 'Kebijakan Kepala Sekolah';
            $jalurSppDiscPct = max(0, $totalSppDiscPct - $jurusanSppBonus);
        } else {
            $jalurLower = strtolower($jalurNama);
            $isUmum = str_contains($jalurLower, 'umum');
            $isInklusi = str_contains($subJalur, 'inklusi') || str_contains($jalurLower, 'inklusi');

            if ($isUmum) {
                if ($isGelombangKhusus) {
                    $jalurSppDiscPct = 15;
                } elseif ($isGelombang1) {
                    $jalurSppDiscPct = 10;
                } elseif ($isGelombang2) {
                    $jalurSppDiscPct = 5;
                } else {
                    $jalurSppDiscPct = 5;
                }
                $sppDuration = '3 Tahun';
                $sppDiscSource = "{$gelombangNama} + Jurusan";
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif ($isInklusi) {
                $jalurSppDiscPct = 0;
                $sppDuration = 'Kebijakan Kepala Sekolah';
                $sppDiscSource = 'Jalur Inklusi (Kebijakan Kepsek) + Jurusan';
                $totalSppDiscPct = $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'anak_guru')) {
                $jalurSppDiscPct = 70;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Anak Guru/Staf (70%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'mou')) {
                if ($pendaftaran->mou_tepat_waktu === false) {
                    $jalurSppDiscPct = 10;
                    $jurusanSppBonus = 0;
                    $sppDuration = '3 Tahun';
                    $sppDiscSource = 'MOU Lewat Periode (10%)';
                    $totalSppDiscPct = 10;
                } else {
                    $jalurSppDiscPct = 30;
                    $sppDuration = '3 Tahun';
                    $sppDiscSource = 'Warga/SMP MOU (30%) + Jurusan';
                    $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
                }
            } elseif (str_contains($subJalur, 'panti')) {
                $jalurSppDiscPct = 25;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Panti Asuhan MOU (25%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'kembar') || str_contains($subJalur, 'saudara')) {
                $jalurSppDiscPct = 20;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Keluarga Warga Sekolah (20%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'ojol')) {
                $jalurSppDiscPct = 20;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Putra/Putri Ojol (20%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'internasional')) {
                $jalurSppDiscPct = 25;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Prestasi Internasional (25%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'prov') || str_contains($subJalur, 'nasional')) {
                $jalurSppDiscPct = 20;
                $sppDuration = '3 Tahun';
                $sppDiscSource = 'Prestasi Prov/Nasional (20%) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } elseif (str_contains($subJalur, 'sekolah') || str_contains($subJalur, 'desa') || str_contains($subJalur, 'kab')) {
                $jalurSppDiscPct = 15;
                $sppDuration = '1 Tahun';
                $sppDiscSource = 'Prestasi Kelas/Desa/Kab (15% 1 Thn) + Jurusan';
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            } else {
                // Prestasi default (15%), Afirmasi (15%), KIP (15%), Minat Bakat (15%), Kerabat Guru (15%)
                $jalurSppDiscPct = 15;
                $sppDuration = '3 Tahun';
                $sppDiscSource = "{$jalurNama} + Jurusan";
                $totalSppDiscPct = $jalurSppDiscPct + $jurusanSppBonus;
            }
        }
        $sppBersih = (int) round($sppDasar - ($sppDasar * ($totalSppDiscPct / 100)));

        // 7. OSIS
        $osisBersih = 25000;

        // Ambil pembayaran yang sudah masuk per kategori
        $paidPerCategory = $this->getPaidAmountsGrouped($pendaftaran->no_registrasi);

        $paidPendaftaran = (int) round($paidPerCategory['pendaftaran'] ?? 0);
        $paidMos = (int) round($paidPerCategory['mos'] ?? 0);
        $paidAsuransi = (int) round($paidPerCategory['asuransi'] ?? 0);
        $paidSeragam = (int) round($paidPerCategory['seragam'] ?? 0);
        $paidDpp = (int) round($paidPerCategory['dpp'] ?? 0);
        $paidSpp = (int) round($paidPerCategory['spp'] ?? 0);
        $paidOsis = (int) round($paidPerCategory['osis'] ?? 0);

        $categories = [
            'pendaftaran' => [
                'id' => 'pendaftaran',
                'nama' => 'Pendaftaran',
                'dasar' => (int) $pendaftaranDasar,
                'diskon_persen' => (float) $regDiscPct,
                'diskon_nominal' => (int) ($pendaftaranDasar - $pendaftaranBersih),
                'tagihan_bersih' => (int) $pendaftaranBersih,
                'sudah_dibayar' => $paidPendaftaran,
                'sisa' => max(0, $pendaftaranBersih - $paidPendaftaran),
                'is_lunas' => $paidPendaftaran >= $pendaftaranBersih,
                'is_refundable' => false,
                'catatan' => $regDiscPct > 0 ? "Diskon {$regDiscPct}% ({$gelombangNama})" : 'Tarif Normal',
            ],
            'mos' => [
                'id' => 'mos',
                'nama' => 'MOS, Kartu Pelajar, Sampul Raport & Kepramukaan',
                'dasar' => (int) $mosBersih,
                'diskon_persen' => 0,
                'diskon_nominal' => 0,
                'tagihan_bersih' => (int) $mosBersih,
                'sudah_dibayar' => $paidMos,
                'sisa' => max(0, $mosBersih - $paidMos),
                'is_lunas' => $paidMos >= $mosBersih,
                'is_refundable' => true,
                'catatan' => 'Harga Tetap',
            ],
            'asuransi' => [
                'id' => 'asuransi',
                'nama' => 'Asuransi (3 Tahun)',
                'dasar' => (int) $asuransiBersih,
                'diskon_persen' => 0,
                'diskon_nominal' => 0,
                'tagihan_bersih' => (int) $asuransiBersih,
                'sudah_dibayar' => $paidAsuransi,
                'sisa' => max(0, $asuransiBersih - $paidAsuransi),
                'is_lunas' => $paidAsuransi >= $asuransiBersih,
                'is_refundable' => true,
                'catatan' => 'Harga Tetap (3 Tahun)',
            ],
            'seragam' => [
                'id' => 'seragam',
                'nama' => 'Seragam / Baju',
                'dasar' => (int) $seragamBersih,
                'diskon_persen' => 0,
                'diskon_nominal' => 0,
                'tagihan_bersih' => (int) $seragamBersih,
                'sudah_dibayar' => $paidSeragam,
                'sisa' => max(0, $seragamBersih - $paidSeragam),
                'is_lunas' => $paidSeragam >= $seragamBersih,
                'is_refundable' => false,
                'catatan' => 'Harga Tetap (Syarat Ambil Baju)',
            ],
            'dpp' => [
                'id' => 'dpp',
                'nama' => 'DPP Semester 1',
                'dasar' => (int) $dppDasar,
                'diskon_persen' => (float) $dppDiscPct,
                'diskon_nominal' => (int) ($dppDasar - $dppBersih),
                'tagihan_bersih' => (int) $dppBersih,
                'sudah_dibayar' => $paidDpp,
                'sisa' => max(0, $dppBersih - $paidDpp),
                'is_lunas' => $paidDpp >= $dppBersih,
                'is_refundable' => true,
                'durasi' => $dppDuration,
                'catatan' => "Diskon {$dppDiscPct}% ({$dppDiscSource})",
            ],
            'spp' => [
                'id' => 'spp',
                'nama' => 'SPP Bulan Pertama',
                'dasar' => (int) $sppDasar,
                'diskon_persen' => (float) $totalSppDiscPct,
                'diskon_nominal' => (int) ($sppDasar - $sppBersih),
                'tagihan_bersih' => (int) $sppBersih,
                'sudah_dibayar' => $paidSpp,
                'sisa' => max(0, $sppBersih - $paidSpp),
                'is_lunas' => $paidSpp >= $sppBersih,
                'is_refundable' => true,
                'durasi' => $sppDuration,
                'catatan' => "Diskon {$totalSppDiscPct}% ({$sppDiscSource})",
                'jalur_persen' => $jalurSppDiscPct,
                'jurusan_persen' => $jurusanSppBonus,
            ],
            'osis' => [
                'id' => 'osis',
                'nama' => 'OSIS Bulan Pertama',
                'dasar' => (int) $osisBersih,
                'diskon_persen' => 0,
                'diskon_nominal' => 0,
                'tagihan_bersih' => (int) $osisBersih,
                'sudah_dibayar' => $paidOsis,
                'sisa' => max(0, $osisBersih - $paidOsis),
                'is_lunas' => $paidOsis >= $osisBersih,
                'is_refundable' => true,
                'catatan' => 'Harga Tetap',
            ],
        ];

        $totalDasar = (int) array_sum(array_column($categories, 'dasar'));
        $totalTagihanBersih = (int) array_sum(array_column($categories, 'tagihan_bersih'));
        $totalSudahDibayar = (int) array_sum(array_column($categories, 'sudah_dibayar'));
        $totalSisa = (int) array_sum(array_column($categories, 'sisa'));
        $totalDiskon = (int) ($totalDasar - $totalTagihanBersih);

        // Status Baju
        $statusBaju = $categories['seragam']['is_lunas'] ? 'Siap Ambil Baju' : 'Belum Lunas';

        // Sesuai Poin 4 Halaman 1 PDF:
        // Calon siswa dinyatakan sudah registrasi ulang jika melakukan pembayaran minimal:
        // MOS + Kartu Pelajar + Sampul Raport + Kepramukaan (450.000), Asuransi (100.000), Seragam (1.975.000), DPP + SPP Semester I
        $minimalDaftarUlang = (int) ($mosBersih + $asuransiBersih + $seragamBersih + $dppBersih + $sppBersih);

        // Status Daftar Ulang
        if ($keuangan->is_refunded) {
            $statusDaftarUlang = 'Direfund (Sekolah Negeri)';
        } elseif ($totalSudahDibayar <= 0) {
            $statusDaftarUlang = 'Belum Daftar Ulang';
        } elseif ($totalSisa <= 0 || $totalSudahDibayar >= $minimalDaftarUlang) {
            $statusDaftarUlang = 'Sah Daftar Ulang';
        } else {
            $statusDaftarUlang = 'Nyicil / Belum Sah';
        }

        // Hitung estimasi Refund jika diterima sekolah negeri (Poin 5 PDF)
        // Total Uang Kembali = Total sudah dibayar - (Pembayaran Pendaftaran + Pembayaran Seragam)
        $paidPendaftaran = $categories['pendaftaran']['sudah_dibayar'];
        $paidSeragam = $categories['seragam']['sudah_dibayar'];
        $nonRefundableTotal = (int) ($paidPendaftaran + $paidSeragam);
        $estimasiRefund = (int) max(0, $totalSudahDibayar - $nonRefundableTotal);

        return [
            'keuangan' => $keuangan,
            'categories' => $categories,
            'total_dasar' => $totalDasar,
            'total_tagihan_bersih' => $totalTagihanBersih,
            'total_sudah_dibayar' => $totalSudahDibayar,
            'total_sisa' => $totalSisa,
            'total_diskon' => $totalDiskon,
            'status_baju' => $statusBaju,
            'status_daftar_ulang' => $statusDaftarUlang,
            'minimal_daftar_ulang' => $minimalDaftarUlang,
            'durasi_spp' => $sppDuration,
            'durasi_dpp' => $dppDuration,
            'diskon_jalur_persen' => $jalurSppDiscPct,
            'diskon_jurusan_persen' => $jurusanSppBonus,
            'diskon_pendaftaran_persen' => $regDiscPct,
            'estimasi_refund' => $estimasiRefund,
            'non_refundable_total' => $nonRefundableTotal,
            'is_tkc' => $isTkc,
            'is_tjkt' => $isTjkt,
        ];
    }

    /**
     * Dapatkan atau buat entri keuangan_siswa secara aman
     */
    public function getOrCreateKeuangan(Pendaftaran $pendaftaran): KeuanganSiswa
    {
        return KeuanganSiswa::firstOrCreate(
            ['no_registrasi' => $pendaftaran->no_registrasi],
            [
                'status_daftar_ulang' => 'Belum Daftar Ulang',
                'status_baju' => 'Belum Lunas',
                'is_refunded' => false,
                'refund_amount' => 0,
            ]
        );
    }

    /**
     * Ambil akumulasi pembayaran masuk per kategori
     */
    public function getPaidAmountsGrouped(string $noRegistrasi): array
    {
        $rows = DB::table('pembayaran_siswa_item')
            ->join('pembayaran_siswa', 'pembayaran_siswa.id', '=', 'pembayaran_siswa_item.pembayaran_id')
            ->where('pembayaran_siswa.no_registrasi', $noRegistrasi)
            ->where('pembayaran_siswa.tipe_transaksi', 'pembayaran')
            ->select('pembayaran_siswa_item.kategori', DB::raw('SUM(pembayaran_siswa_item.nominal) as total'))
            ->groupBy('pembayaran_siswa_item.kategori')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[$row->kategori] = (int) round((float) $row->total);
        }

        return $result;
    }

    /**
     * Ambil ringkasan kas per pos biaya keseluruhan untuk summary cards
     * HANYA siswa yang berstatus berkas Terverifikasi (Diterima) yang dihitung tagihan & piutangnya.
     */
    public function getGlobalFinancialSummary(Collection $pendaftarans): array
    {
        $kasPerKategori = [
            'pendaftaran' => 0,
            'mos' => 0,
            'asuransi' => 0,
            'seragam' => 0,
            'dpp' => 0,
            'spp' => 0,
            'osis' => 0,
        ];

        // Ambil data pembayaran riil dari DB
        $allPaidItems = DB::table('pembayaran_siswa_item')
            ->join('pembayaran_siswa', 'pembayaran_siswa.id', '=', 'pembayaran_siswa_item.pembayaran_id')
            ->where('pembayaran_siswa.tipe_transaksi', 'pembayaran')
            ->select('pembayaran_siswa_item.kategori', DB::raw('SUM(pembayaran_siswa_item.nominal) as total'))
            ->groupBy('pembayaran_siswa_item.kategori')
            ->get();

        foreach ($allPaidItems as $item) {
            if (isset($kasPerKategori[$item->kategori])) {
                $kasPerKategori[$item->kategori] = (int) round((float) $item->total);
            }
        }

        $totalPemasukan = (int) array_sum($kasPerKategori);
        $totalTagihanKeseluruhan = 0;
        $totalPiutang = 0;
        $siswaLunasCount = 0;
        $siswaNyicilCount = 0;
        $siswaBelumCount = 0;
        $siswaRefundCount = 0;
        $siswaBajuLunasCount = 0;

        foreach ($pendaftarans as $p) {
            // STATE MACHINE GUARD: Hanya siswa yang status berkasnya Terverifikasi (Diterima)
            // yang memiliki tagihan aktif dan dihitung ke dalam piutang sekolah.
            if ($p->status_berkas !== 'Diterima') {
                continue;
            }

            $billing = $this->calculateStudentBilling($p);
            $totalTagihanKeseluruhan += (int) $billing['total_tagihan_bersih'];
            $totalPiutang += (int) $billing['total_sisa'];

            if ($billing['status_daftar_ulang'] === 'Sah Daftar Ulang') {
                $siswaLunasCount++;
            } elseif ($billing['status_daftar_ulang'] === 'Nyicil / Belum Sah') {
                $siswaNyicilCount++;
            } elseif ($billing['status_daftar_ulang'] === 'Direfund (Sekolah Negeri)') {
                $siswaRefundCount++;
            } else {
                $siswaBelumCount++;
            }

            if ($billing['status_baju'] === 'Siap Ambil Baju') {
                $siswaBajuLunasCount++;
            }
        }

        return [
            'total_pemasukan' => $totalPemasukan,
            'total_tagihan' => $totalTagihanKeseluruhan,
            'total_piutang' => $totalPiutang,
            'siswa_lunas_count' => $siswaLunasCount,
            'siswa_nyicil_count' => $siswaNyicilCount,
            'siswa_belum_count' => $siswaBelumCount,
            'siswa_refund_count' => $siswaRefundCount,
            'siswa_baju_lunas_count' => $siswaBajuLunasCount,
            'kas_kategori' => $kasPerKategori,
        ];
    }

    /**
     * Catat pembayaran baru dengan alokasi ke 7 kategori
     */
    public function recordPayment(
        Pendaftaran $pendaftaran,
        array $allocations,
        string $metodePembayaran,
        ?string $tglBayar = null,
        ?string $catatan = null,
        ?string $namaPetugas = null
    ): PembayaranSiswa {
        if ($pendaftaran->status_berkas !== 'Diterima') {
            throw new \InvalidArgumentException('Transaksi pembayaran hanya dapat dicatat untuk calon siswa yang status berkasnya sudah Terverifikasi (Diterima).');
        }

        return DB::transaction(function () use (
            $pendaftaran,
            $allocations,
            $metodePembayaran,
            $tglBayar,
            $catatan,
            $namaPetugas
        ) {
            $totalBayar = 0;
            $validItems = [];

            foreach (self::KATEGORI_CONFIG as $catKey => $config) {
                $nominal = (int) round((float) ($allocations[$catKey] ?? 0));
                if ($nominal > 0) {
                    $totalBayar += $nominal;
                    $validItems[$catKey] = $nominal;
                }
            }

            if ($totalBayar <= 0) {
                throw new \InvalidArgumentException('Nominal pembayaran minimal harus lebih dari Rp 0.');
            }

            // Generate nomor kuitansi unik: KWT-YYYYMMDD-XXXX
            $noKuitansi = $this->generateReceiptNumber('KWT');

            // Buat header transaksi
            $pembayaran = PembayaranSiswa::create([
                'no_kuitansi' => $noKuitansi,
                'no_registrasi' => $pendaftaran->no_registrasi,
                'tipe_transaksi' => 'pembayaran',
                'total_bayar' => $totalBayar,
                'metode_pembayaran' => $metodePembayaran,
                'tgl_bayar' => $tglBayar ?: Carbon::now('Asia/Makassar')->format('Y-m-d'),
                'catatan' => $catatan,
                'nama_petugas' => $namaPetugas ?: 'Petugas Keuangan SPMB',
            ]);

            // Buat item rincian pos biaya
            foreach ($validItems as $catKey => $nominal) {
                PembayaranSiswaItem::create([
                    'pembayaran_id' => $pembayaran->id,
                    'kategori' => $catKey,
                    'nominal' => $nominal,
                ]);
            }

            // Sync status keuangan siswa
            $this->syncKeuanganStatus($pendaftaran);

            // Log aktivitas admin
            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $pendaftaran->no_registrasi;
            AdminActivityLog::create([
                'admin_name' => $namaPetugas ?: 'Petugas Keuangan',
                'action_type' => 'keuangan',
                'title' => 'Mencatat Pembayaran Siswa',
                'description' => 'Menerima pembayaran Rp '.number_format($totalBayar, 0, ',', '.')." untuk siswa {$studentName} (No. Reg: {$pendaftaran->no_registrasi}, Kuitansi: {$noKuitansi}).",
                'badge_label' => 'Uang Masuk',
                'badge_color' => 'blue',
                'icon' => 'check-circle',
                'no_registrasi' => $pendaftaran->no_registrasi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $pembayaran;
        });
    }

    /**
     * Batalkan / Void 1 kuitansi pembayaran tertentu
     */
    public function voidKuitansi(string $noKuitansi, string $alasan, ?string $adminName = null): array
    {
        return DB::transaction(function () use ($noKuitansi, $alasan, $adminName) {
            $pembayaran = PembayaranSiswa::where('no_kuitansi', $noKuitansi)->firstOrFail();
            $pendaftaran = Pendaftaran::where('no_registrasi', $pembayaran->no_registrasi)->firstOrFail();
            $totalBayar = (int) $pembayaran->total_bayar;
            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $pendaftaran->no_registrasi;
            $noReg = $pendaftaran->no_registrasi;

            // Hapus rincian item & kuitansi
            $pembayaran->items()->delete();
            $pembayaran->delete();

            // Sinkronkan ulang status keuangan & daftar ulang
            $this->syncKeuanganStatus($pendaftaran);

            // Catat log aktivitas audit
            AdminActivityLog::record(
                actionType: 'keuangan',
                title: 'Void / Batalkan Kuitansi',
                description: "Membatalkan kuitansi {$noKuitansi} sejumlah Rp ".number_format($totalBayar, 0, ',', '.')." untuk siswa {$studentName} (No. Reg: {$noReg}). Alasan: \"{$alasan}\"",
                noRegistrasi: $noReg,
                badgeLabel: 'Void Kuitansi',
                badgeColor: 'rose',
                icon: 'x-circle',
                adminName: $adminName ?: (auth()->user()?->name ?: 'Admin Keuangan')
            );

            return $this->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));
        });
    }

    /**
     * Reset / Void seluruh pembayaran siswa kembali ke Rp 0
     */
    public function resetStudentPayments(Pendaftaran $pendaftaran, string $alasan, ?string $adminName = null): array
    {
        return DB::transaction(function () use ($pendaftaran, $alasan, $adminName) {
            $noReg = $pendaftaran->no_registrasi;
            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $noReg;

            $payments = PembayaranSiswa::where('no_registrasi', $noReg)->get();
            $totalReset = (int) $payments->where('tipe_transaksi', 'pembayaran')->sum('total_bayar');

            foreach ($payments as $payment) {
                $payment->items()->delete();
                $payment->delete();
            }

            $keuangan = $this->getOrCreateKeuangan($pendaftaran);
            $keuangan->update([
                'status_daftar_ulang' => 'Belum Daftar Ulang',
                'status_baju' => 'Belum Lunas',
                'is_refunded' => false,
                'refund_amount' => 0,
                'refund_date' => null,
                'refund_notes' => null,
            ]);

            Cache::put('daftar_ulang_status_'.$noReg, 'Menunggu', now()->addMonths(6));
            Cache::put('daftar_ulang_pembayaran_'.$noReg, 'Belum', now()->addMonths(6));
            Cache::forget('daftar_ulang_date_'.$noReg);

            AdminActivityLog::record(
                actionType: 'keuangan',
                title: 'Reset Seluruh Pembayaran Siswa',
                description: "Mereset seluruh pembayaran siswa {$studentName} (No. Reg: {$noReg}) kembali ke Rp 0 (Sebelumnya: Rp ".number_format($totalReset, 0, ',', '.')."). Alasan: \"{$alasan}\"",
                noRegistrasi: $noReg,
                badgeLabel: 'Reset Tagihan',
                badgeColor: 'rose',
                icon: 'alert-triangle',
                adminName: $adminName ?: (auth()->user()?->name ?: 'Admin Keuangan')
            );

            return $this->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));
        });
    }

    /**
     * Hapus data siswa uji coba (dummy) beserta riwayat tagihannya (Khusus Admin Utama)
     */
    public function deleteStudentWithFinances(Pendaftaran $pendaftaran, ?string $adminName = null): void
    {
        DB::transaction(function () use ($pendaftaran, $adminName) {
            $noReg = $pendaftaran->no_registrasi;
            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $noReg;
            $calonSiswa = $pendaftaran->calonSiswa;

            // Hapus pembayaran & item
            $payments = PembayaranSiswa::where('no_registrasi', $noReg)->get();
            foreach ($payments as $pay) {
                $pay->items()->delete();
                $pay->delete();
            }

            KeuanganSiswa::where('no_registrasi', $noReg)->delete();
            BerkasPersyaratan::where('no_registrasi', $noReg)->delete();
            VerifikasiPanitia::where('no_registrasi', $noReg)->delete();
            $pendaftaran->delete();

            if ($calonSiswa) {
                $userId = $calonSiswa->id_user;
                $calonSiswa->delete();
                if ($userId) {
                    User::where('id_user', $userId)->where('role', 'siswa')->delete();
                }
            }

            Cache::forget('daftar_ulang_status_'.$noReg);
            Cache::forget('daftar_ulang_date_'.$noReg);
            Cache::forget('daftar_ulang_pembayaran_'.$noReg);

            AdminActivityLog::record(
                actionType: 'keuangan',
                title: 'Menghapus Data Siswa & Tagihan',
                description: "Menghapus data siswa {$studentName} (No. Reg: {$noReg}) beserta seluruh riwayat tagihan & pembayaran.",
                noRegistrasi: $noReg,
                badgeLabel: 'Hapus Data',
                badgeColor: 'rose',
                icon: 'trash',
                adminName: $adminName ?: (auth()->user()?->name ?: 'Admin Utama')
            );
        });
    }

    /**
     * Hitung alokasi proporsional nominal pembayaran ke kategori yang masih ada sisa
     * Menggunakan pembulatan integer dan alokasikan selisih sisa ke pos terakhir.
     */
    public function allocateProportional(int $totalBayar, array $categories): array
    {
        if ($totalBayar <= 0) {
            return [];
        }

        $activeCategories = [];
        $totalSisa = 0;
        foreach ($categories as $catKey => $cat) {
            $sisa = (int) ($cat['sisa'] ?? 0);
            if ($sisa > 0) {
                $activeCategories[$catKey] = $sisa;
                $totalSisa += $sisa;
            }
        }

        if ($totalSisa <= 0) {
            return [];
        }

        // Jika bayar penuh atau lebih dari sisa
        if ($totalBayar >= $totalSisa) {
            return $activeCategories;
        }

        $allocated = [];
        $allocatedSum = 0;
        $keys = array_keys($activeCategories);
        $lastKey = end($keys);

        foreach ($activeCategories as $catKey => $sisa) {
            if ($catKey === $lastKey) {
                // Pos terakhir menyerap sisa agar total 100% pas
                $allocated[$catKey] = max(0, $totalBayar - $allocatedSum);
            } else {
                $share = (int) round(($sisa / $totalSisa) * $totalBayar);
                $allocated[$catKey] = min($sisa, $share);
                $allocatedSum += $allocated[$catKey];
            }
        }

        return $allocated;
    }

    /**
     * Proses refund dana siswa yang diterima di sekolah negeri
     * Aturan: Pengembalian = Total dibayar - (Pembayaran Pendaftaran + Pembayaran Seragam)
     */
    public function processRefund(
        Pendaftaran $pendaftaran,
        ?string $alasan = null,
        ?string $namaPetugas = null
    ): array {
        return DB::transaction(function () use ($pendaftaran, $alasan, $namaPetugas) {
            $billing = $this->calculateStudentBilling($pendaftaran);
            $totalSudahDibayar = $billing['total_sudah_dibayar'];
            $paidPendaftaran = $billing['categories']['pendaftaran']['sudah_dibayar'];
            $paidSeragam = $billing['categories']['seragam']['sudah_dibayar'];
            $nonRefundable = $paidPendaftaran + $paidSeragam;
            $refundAmount = max(0, $totalSudahDibayar - $nonRefundable);

            $keuangan = $this->getOrCreateKeuangan($pendaftaran);
            $keuangan->update([
                'is_refunded' => true,
                'refund_amount' => $refundAmount,
                'refund_date' => Carbon::now('Asia/Makassar'),
                'refund_notes' => $alasan ?: 'Diterima di Sekolah Negeri (Sebelum MPLS Selesai)',
                'status_daftar_ulang' => 'Direfund (Sekolah Negeri)',
            ]);

            Cache::put('daftar_ulang_status_'.$pendaftaran->no_registrasi, 'Menunggu', now()->addMonths(6));
            Cache::put('daftar_ulang_pembayaran_'.$pendaftaran->no_registrasi, 'Belum', now()->addMonths(6));
            Cache::forget('daftar_ulang_date_'.$pendaftaran->no_registrasi);

            // Buat record transaksi refund untuk pembukuan kuitansi
            $noKuitansiRefund = $this->generateReceiptNumber('RFD');

            PembayaranSiswa::create([
                'no_kuitansi' => $noKuitansiRefund,
                'no_registrasi' => $pendaftaran->no_registrasi,
                'tipe_transaksi' => 'refund',
                'total_bayar' => $refundAmount,
                'metode_pembayaran' => 'Transfer Pengembalian',
                'tgl_bayar' => Carbon::now('Asia/Makassar')->format('Y-m-d'),
                'catatan' => 'Pengembalian dana (Diterima di Sekolah Negeri). Total masuk: Rp '.number_format($totalSudahDibayar, 0, ',', '.').', Hangus (Pendaftaran + Seragam): Rp '.number_format($nonRefundable, 0, ',', '.').". Alasan: {$alasan}",
                'nama_petugas' => $namaPetugas ?: 'Petugas Keuangan SPMB',
            ]);

            // Log aktivitas admin
            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $pendaftaran->no_registrasi;
            AdminActivityLog::create([
                'admin_name' => $namaPetugas ?: 'Petugas Keuangan',
                'action_type' => 'refund',
                'title' => 'Memproses Refund Siswa (Diterima Negeri)',
                'description' => "Memproses refund dana siswa {$studentName} sebesar Rp ".number_format($refundAmount, 0, ',', '.').' (Diterima di Sekolah Negeri). Non-refundable Seragam & Pendaftaran Rp '.number_format($nonRefundable, 0, ',', '.').'.',
                'badge_label' => 'Refund Selesai',
                'badge_color' => 'rose',
                'icon' => 'arrow-uturn-left',
                'no_registrasi' => $pendaftaran->no_registrasi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'refund_amount' => $refundAmount,
                'non_refundable' => $nonRefundable,
                'total_bayar_sebelumnya' => $totalSudahDibayar,
                'no_kuitansi_refund' => $noKuitansiRefund,
            ];
        });
    }

    /**
     * Perbarui penyesuaian diskon SPP manual oleh Admin (Kebijakan Kepala Sekolah)
     */
    public function updateCustomSppDiscount(
        Pendaftaran $pendaftaran,
        float $diskonSppPct,
        ?string $catatan = null,
        ?string $namaPetugas = null
    ): void {
        $keuangan = $this->getOrCreateKeuangan($pendaftaran);
        $keuangan->update([
            'diskon_spp_custom' => $diskonSppPct,
            'catatan_diskon' => $catatan ?: 'Penyesuaian diskon SPP sesuai kebijakan Kepala Sekolah',
        ]);

        $this->syncKeuanganStatus($pendaftaran);

        $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $pendaftaran->no_registrasi;
        AdminActivityLog::create([
            'admin_name' => $namaPetugas ?: 'Petugas Keuangan',
            'action_type' => 'diskon',
            'title' => 'Penyesuaian Diskon SPP Manual',
            'description' => "Menyesuaikan diskon SPP untuk calon siswa {$studentName} menjadi {$diskonSppPct}% (Kebijakan Kepala Sekolah). Catatan: {$catatan}",
            'badge_label' => 'Diskon Kepsek',
            'badge_color' => 'amber',
            'icon' => 'tag',
            'no_registrasi' => $pendaftaran->no_registrasi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Sinkronkan status_baju dan status_daftar_ulang di tabel keuangan_siswa serta modul daftar ulang
     */
    public function syncKeuanganStatus(Pendaftaran $pendaftaran): void
    {
        $billing = $this->calculateStudentBilling($pendaftaran);
        $keuangan = $this->getOrCreateKeuangan($pendaftaran);

        $keuangan->update([
            'status_baju' => $billing['status_baju'],
            'status_daftar_ulang' => $billing['status_daftar_ulang'],
            'minimal_daftar_ulang' => $billing['minimal_daftar_ulang'],
            'durasi_spp' => $billing['durasi_spp'],
            'durasi_dpp' => $billing['durasi_dpp'],
            'diskon_jalur_persen' => $billing['diskon_jalur_persen'],
            'diskon_jurusan_persen' => $billing['diskon_jurusan_persen'],
            'diskon_pendaftaran_persen' => $billing['diskon_pendaftaran_persen'],
        ]);

        $noReg = $pendaftaran->no_registrasi;
        $nowDateTime = Carbon::now('Asia/Makassar')->format('Y-m-d H:i:s');

        if ($keuangan->is_refunded) {
            Cache::put('daftar_ulang_status_'.$noReg, 'Menunggu', now()->addMonths(6));
            Cache::put('daftar_ulang_pembayaran_'.$noReg, 'Belum', now()->addMonths(6));
            Cache::forget('daftar_ulang_date_'.$noReg);
        } elseif ($billing['total_sisa'] <= 0 && $billing['total_tagihan_bersih'] > 0) {
            // Lunas penuh di Keuangan -> Otomatis Lunas dan Sudah Daftar Ulang
            Cache::put('daftar_ulang_status_'.$noReg, 'Sudah Daftar Ulang', now()->addMonths(6));
            Cache::put('daftar_ulang_pembayaran_'.$noReg, 'Lunas', now()->addMonths(6));
            if (! Cache::has('daftar_ulang_date_'.$noReg)) {
                Cache::put('daftar_ulang_date_'.$noReg, $nowDateTime, now()->addMonths(6));
            }
        } elseif ($billing['total_sudah_dibayar'] > 0) {
            // Pembayaran cicil (Nyicil)
            Cache::put('daftar_ulang_pembayaran_'.$noReg, 'Nyicil', now()->addMonths(6));
            if ($billing['total_sudah_dibayar'] >= $billing['minimal_daftar_ulang']) {
                Cache::put('daftar_ulang_status_'.$noReg, 'Sudah Daftar Ulang', now()->addMonths(6));
                if (! Cache::has('daftar_ulang_date_'.$noReg)) {
                    Cache::put('daftar_ulang_date_'.$noReg, $nowDateTime, now()->addMonths(6));
                }
            } else {
                Cache::put('daftar_ulang_status_'.$noReg, 'Menunggu', now()->addMonths(6));
            }
        } else {
            // Belum bayar sama sekali / direset ke 0
            Cache::put('daftar_ulang_status_'.$noReg, 'Menunggu', now()->addMonths(6));
            Cache::put('daftar_ulang_pembayaran_'.$noReg, 'Belum', now()->addMonths(6));
            Cache::forget('daftar_ulang_date_'.$noReg);
        }
    }

    /**
     * Konversi angka nominal ke kalimat terbilang Bahasa Indonesia
     */
    public static function terbilang(float|int $angka): string
    {
        $angka = abs((int) $angka);
        $baca = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas',
        ];

        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return self::terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            return self::terbilang((int) ($angka / 10)).' Puluh '.self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'Seratus '.self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return self::terbilang((int) ($angka / 100)).' Ratus '.self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu '.self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::terbilang((int) ($angka / 1000)).' Ribu '.self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::terbilang((int) ($angka / 1000000)).' Juta '.self::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return self::terbilang((int) ($angka / 1000000000)).' Milyar '.self::terbilang(fmod($angka, 1000000000));
        }

        return '';
    }

    /**
     * Generate nomor kuitansi unik berurutan: KWT-YYYYMMDD-XXXX atau RFD-YYYYMMDD-XXXX
     */
    public function generateReceiptNumber(string $prefixType = 'KWT'): string
    {
        $todayStr = Carbon::now('Asia/Makassar')->format('Ymd');
        $prefix = sprintf('%s-%s-', $prefixType, $todayStr);

        $lastRecord = PembayaranSiswa::where('no_kuitansi', 'like', $prefix.'%')
            ->orderByDesc('no_kuitansi')
            ->lockForUpdate()
            ->first();

        if ($lastRecord) {
            $lastSeq = (int) substr($lastRecord->no_kuitansi, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        $noKuitansi = sprintf('%s%04d', $prefix, $nextSeq);

        while (PembayaranSiswa::where('no_kuitansi', $noKuitansi)->exists()) {
            $nextSeq++;
            $noKuitansi = sprintf('%s%04d', $prefix, $nextSeq);
        }

        return $noKuitansi;
    }
}
