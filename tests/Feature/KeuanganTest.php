<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\KeuanganService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KeuanganTest extends TestCase
{
    private function createAdmin(): User
    {
        return User::create([
            'email' => 'admin_keu_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
    }

    private function createStudentWithPendaftaran(string $jurusanNama = 'PPLG (Pengembangan Perangkat Lunak & Gim)', string $gelombangNama = 'Gelombang 1'): array
    {
        $studentUser = User::create([
            'email' => 'student_keu_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $studentUser->id_user,
            'nama_lengkap' => 'Tester Siswa Keuangan',
            'nisn' => (string) rand(1000000000, 9999999999),
            'asal_sekolah' => 'SMP Negeri 1 Denpasar',
            'no_whatsapp' => '081234567890',
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'alamat_domisili' => 'Jl. Veteran No. 1, Denpasar',
        ]);

        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => $jurusanNama], ['kuota' => 72]);
        $jalur = JalurPendaftaran::firstOrCreate(['nama_jalur' => 'Jalur Prestasi']);
        $gelombang = GelombangPendaftaran::firstOrCreate(['nama_gelombang' => $gelombangNama], [
            'tgl_buka' => '2026-01-01',
            'tgl_tutup' => '2026-06-30',
            'keterangan_diskon' => 'Diskon Gelombang',
        ]);

        $noReg = (string) rand(800, 999);
        // Ensure no collision
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = (string) rand(800, 999);
        }

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => $jurusan->id_jurusan,
            'id_jalur' => $jalur->id_jalur,
            'id_gelombang' => $gelombang->id_gelombang,
            'tgl_daftar' => now(),
            'status_berkas' => 'Diterima',
        ]);

        return [$pendaftaran, $calonSiswa, $studentUser];
    }

    protected function tearDown(): void
    {
        Pendaftaran::whereHas('calonSiswa', fn ($q) => $q->where('nama_lengkap', 'like', '%Tester Siswa Keuangan%'))->delete();
        CalonSiswa::where('nama_lengkap', 'like', '%Tester Siswa Keuangan%')->delete();
        User::where('email', 'like', '%student_keu_%')->orWhere('email', 'like', '%admin_keu_%')->delete();

        parent::tearDown();
    }

    public function test_guest_is_redirected_from_keuangan_page(): void
    {
        $response = $this->get('/admin/keuangan');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_keuangan_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/keuangan');
        $response->assertStatus(200);
        $response->assertSee('Keuangan');
        $response->assertSee('Alokasi Uang Masuk Per 7 Pos Keuangan');
    }

    public function test_keuangan_service_calculates_7_categories_correctly(): void
    {
        [$pendaftaran] = $this->createStudentWithPendaftaran('PPLG (Pengembangan Perangkat Lunak & Gim)', 'Gelombang 1');

        $service = app(KeuanganService::class);
        $billing = $service->calculateStudentBilling($pendaftaran);

        $cats = $billing['categories'];

        // 1. Pendaftaran: Rp 150.000 dengan diskon 50% di Gelombang 1 = Rp 75.000
        $this->assertEquals(75000, $cats['pendaftaran']['tagihan_bersih']);

        // 2. MOS: Rp 450.000 fixed
        $this->assertEquals(450000, $cats['mos']['tagihan_bersih']);

        // 3. Asuransi: Rp 100.000 fixed
        $this->assertEquals(100000, $cats['asuransi']['tagihan_bersih']);

        // 4. Seragam: Rp 1.975.000 fixed
        $this->assertEquals(1975000, $cats['seragam']['tagihan_bersih']);

        // 5. DPP Semester 1: TI = Rp 650.000 (Prestasi 50% = Rp 325.000)
        $this->assertEquals(325000, $cats['dpp']['tagihan_bersih']);

        // 6. SPP Bulan Pertama: TI = Rp 650.000 (Prestasi 15% + PPLG 5% = 20% disc => Rp 520.000)
        $this->assertEquals(520000, $cats['spp']['tagihan_bersih']);

        // 7. OSIS: Rp 25.000 fixed
        $this->assertEquals(25000, $cats['osis']['tagihan_bersih']);

        // Total Tagihan Bersih = 75k + 450k + 100k + 1975k + 325k + 520k + 25k = 3.470.000
        $this->assertEquals(3470000, $billing['total_tagihan_bersih']);
        $this->assertEquals('Belum Lunas', $billing['status_baju']);
        $this->assertEquals('Belum Daftar Ulang', $billing['status_daftar_ulang']);
    }

    public function test_admin_can_record_payment_with_category_allocations(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        // Bayar seragam 1.975.000 dan OSIS 25.000
        $payload = [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'metode_pembayaran' => 'Transfer Bank BCA',
            'tgl_bayar' => now()->format('Y-m-d'),
            'catatan' => 'Cicilan tahap 1: Seragam dan OSIS',
            'alokasi' => [
                'seragam' => 1975000,
                'osis' => 25000,
            ],
        ];

        $response = $this->actingAs($admin)->postJson('/admin/keuangan/bayar', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $billing = $response->json('billing');
        $this->assertEquals('Siap Ambil Baju', $billing['status_baju']);
        $this->assertEquals('Nyicil / Belum Sah', $billing['status_daftar_ulang']);
        $this->assertEquals(2000000, $billing['total_sudah_dibayar']);
    }

    public function test_full_payment_updates_status_to_sah_daftar_ulang(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        $service = app(KeuanganService::class);
        $billingBefore = $service->calculateStudentBilling($pendaftaran);

        // Bayar lunas seluruh 7 pos biaya
        $allocations = [];
        foreach ($billingBefore['categories'] as $catKey => $cat) {
            $allocations[$catKey] = $cat['tagihan_bersih'];
        }

        $response = $this->actingAs($admin)->postJson('/admin/keuangan/bayar', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'metode_pembayaran' => 'Tunai',
            'tgl_bayar' => now()->format('Y-m-d'),
            'catatan' => 'Pembayaran lunas penuh 7 pos biaya',
            'alokasi' => $allocations,
        ]);

        $response->assertStatus(200);
        $billing = $response->json('billing');
        $this->assertEquals('Siap Ambil Baju', $billing['status_baju']);
        $this->assertEquals('Sah Daftar Ulang', $billing['status_daftar_ulang']);
        $this->assertEquals(0, $billing['total_sisa']);
    }

    public function test_refund_logic_excludes_pendaftaran_and_seragam(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        // Siswa membayar: Pendaftaran (75.000), Seragam (1.975.000), dan MOS (450.000) = Total 2.500.000
        $this->actingAs($admin)->postJson('/admin/keuangan/bayar', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'metode_pembayaran' => 'Transfer Bank BPD Bali',
            'tgl_bayar' => now()->format('Y-m-d'),
            'alokasi' => [
                'pendaftaran' => 75000,
                'seragam' => 1975000,
                'mos' => 450000,
            ],
        ]);

        // Siswa diterima di sekolah negeri -> Proses refund
        // Sesuai aturan: Uang kembali = Total bayar (2.500.000) - (Pendaftaran 75.000 + Seragam 1.975.000) = 450.000 (MOS)
        $response = $this->actingAs($admin)->postJson('/admin/keuangan/refund', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'alasan' => 'Diterima di SMAN 1 Mengwi Jalur Zonasi',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'refund' => [
                'refund_amount' => 450000,
                'non_refundable' => 2050000,
            ],
        ]);

        // Cek status pendaftaran terbaru
        $service = app(KeuanganService::class);
        $billingAfter = $service->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));
        $this->assertEquals('Direfund (Sekolah Negeri)', $billingAfter['status_daftar_ulang']);
    }

    public function test_admin_can_adjust_spp_discount_manually(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        // Ubah diskon SPP menjadi 75% (Kebijakan Kepala Sekolah)
        $response = $this->actingAs($admin)->postJson('/admin/keuangan/diskon-spp', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'diskon_spp' => 75,
            'catatan' => 'Kebijakan Afirmasi Kepala Sekolah',
        ]);

        $response->assertStatus(200);
        $billing = $response->json('billing');
        $this->assertEquals(75, $billing['categories']['spp']['diskon_persen']);
        // Dasar SPP TI = 650.000, Diskon 75% => Bersih = 162.500
        $this->assertEquals(162500, $billing['categories']['spp']['tagihan_bersih']);
    }

    public function test_payment_in_keuangan_automatically_updates_daftar_ulang_status_to_lunas(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        $service = app(KeuanganService::class);
        $billingBefore = $service->calculateStudentBilling($pendaftaran);

        // Bayar lunas semua tagihan di Keuangan
        $allocations = [];
        foreach ($billingBefore['categories'] as $catKey => $cat) {
            $allocations[$catKey] = $cat['tagihan_bersih'];
        }

        $this->actingAs($admin)->postJson('/admin/keuangan/bayar', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'metode_pembayaran' => 'Transfer Bank BPD Bali',
            'tgl_bayar' => now()->format('Y-m-d'),
            'catatan' => 'Pelunasan penuh di modul Keuangan',
            'alokasi' => $allocations,
        ])->assertStatus(200);

        // Akses halaman daftar ulang dan pastikan status daftar ulang & pembayaran sinkron Lunas
        $response = $this->actingAs($admin)->get('/admin/daftar-ulang');
        $response->assertStatus(200);

        $freshPendaftaran = $pendaftaran->fresh(['keuangan', 'pembayaran.items']);
        $billingAfter = $service->calculateStudentBilling($freshPendaftaran);

        $this->assertEquals(0, $billingAfter['total_sisa']);
        $this->assertEquals('Lunas', Cache::get('daftar_ulang_pembayaran_'.$pendaftaran->no_registrasi));
        $this->assertEquals('Sudah Daftar Ulang', Cache::get('daftar_ulang_status_'.$pendaftaran->no_registrasi));
    }

    public function test_setting_lunas_in_daftar_ulang_automatically_settles_balance_in_keuangan(): void
    {
        $admin = $this->createAdmin();
        [$pendaftaran] = $this->createStudentWithPendaftaran();

        $service = app(KeuanganService::class);
        $billingBefore = $service->calculateStudentBilling($pendaftaran);
        $this->assertGreaterThan(0, $billingBefore['total_sisa']);

        // Admin menandai Lunas di modul Daftar Ulang
        $response = $this->actingAs($admin)->postJson('/admin/daftar-ulang/status', [
            'no_registrasi' => $pendaftaran->no_registrasi,
            'status' => 'Sudah Daftar Ulang',
            'status_pembayaran_awal' => 'Lunas',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'Sudah Daftar Ulang',
            'status_pembayaran_awal' => 'Lunas',
        ]);

        // Cek bahwa sisa tagihan di Keuangan otomatis menjadi 0 (lunas)
        $freshPendaftaran = $pendaftaran->fresh(['keuangan', 'pembayaran.items']);
        $billingAfter = $service->calculateStudentBilling($freshPendaftaran);
        $this->assertEquals(0, $billingAfter['total_sisa']);
        $this->assertEquals('Siap Ambil Baju', $billingAfter['status_baju']);
        $this->assertEquals('Sah Daftar Ulang', $billingAfter['status_daftar_ulang']);
    }
}
