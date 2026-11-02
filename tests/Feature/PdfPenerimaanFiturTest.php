<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\KeuanganSiswa;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\KeuanganService;
use App\Services\SyaratBerkasService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfPenerimaanFiturTest extends TestCase
{
    private function createAdminUser(): User
    {
        $user = User::create([
            'email' => 'admin_test_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        Panitia::create([
            'id_user' => $user->id_user,
            'nama_panitia' => 'Admin Panitia SPMB',
            'nip' => 'SPMB'.str_pad((string) rand(1, 999999), 10, '0', STR_PAD_LEFT),
            'no_telepon' => '081234567890',
            'bidang_studi' => 'Panitia Penerimaan',
        ]);

        return $user;
    }

    private function createSiswaUser(): array
    {
        $user = User::create([
            'email' => 'siswa_test_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Calon Siswa Tes PDF',
            'nisn' => (string) rand(1000000000, 9999999999),
            'asal_sekolah' => 'SMP Negeri 2 Kuta',
            'no_whatsapp' => '081234567890',
            'tempat_lahir' => 'Badung',
            'tanggal_lahir' => '2009-05-15',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'alamat_domisili' => 'Jl. Dalung Permai No. 10',
            'nama_bapak' => 'I Wayan Bapak',
            'pekerjaan_bapak' => 'Karyawan Swasta',
            'nama_ibu' => 'Ni Made Ibu',
            'pekerjaan_ibu' => 'Wiraswasta',
            'no_wa_ayah' => '081234567891',
            'no_wa_ibu' => '081234567892',
            'alamat_ortu' => 'Jl. Dalung Permai No. 10',
        ]);

        return [$user, $calonSiswa];
    }

    public function test_syarat_berkas_service_returns_accurate_document_requirements_from_pdf(): void
    {
        // 1. Jalur Umum: berkas dasar (foto, akta, kk)
        $docsUmum = SyaratBerkasService::getRequiredDocuments('Jalur Umum');
        $this->assertArrayHasKey('berkas_foto', $docsUmum);
        $this->assertArrayHasKey('berkas_kk', $docsUmum);
        $this->assertArrayHasKey('berkas_akta', $docsUmum);
        $this->assertArrayNotHasKey('berkas_prestasi', $docsUmum);

        // 2. Jalur Prestasi: harus menyertakan berkas_prestasi
        $docsPrestasi = SyaratBerkasService::getRequiredDocuments('Jalur Prestasi', 'prestasi_prov_nas');
        $this->assertArrayHasKey('berkas_prestasi', $docsPrestasi);

        // 3. Jalur Afirmasi: harus menyertakan berkas_sktm & berkas_permohonan
        $docsAfirmasi = SyaratBerkasService::getRequiredDocuments('Jalur Afirmasi');
        $this->assertArrayHasKey('berkas_sktm', $docsAfirmasi);
        $this->assertArrayHasKey('berkas_permohonan', $docsAfirmasi);

        // 4. Jalur KIP: harus menyertakan berkas_kip
        $docsKip = SyaratBerkasService::getRequiredDocuments('Jalur KIP');
        $this->assertArrayHasKey('berkas_kip', $docsKip);

        // 5. Jalur Khusus Ojek Online: harus menyertakan berkas_ojol
        $docsOjol = SyaratBerkasService::getRequiredDocuments('Jalur Khusus', 'ojol');
        $this->assertArrayHasKey('berkas_ojol', $docsOjol);

        // 6. Voucher: harus menyertakan berkas_voucher
        $docsVoucher = SyaratBerkasService::getRequiredDocuments('Jalur Umum', null, null, 'rekomendasi');
        $this->assertArrayHasKey('berkas_voucher', $docsVoucher);
    }

    public function test_registration_step_2_quota_validation_for_jalur_afirmasi(): void
    {
        [$user, $calonSiswa] = $this->createSiswaUser();
        $this->actingAs($user);

        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'TJKT (Teknik Jaringan Komputer & Telekomunikasi)'], ['kuota' => 72]);
        $jalurAfirmasi = JalurPendaftaran::firstOrCreate(['nama_jalur' => 'Jalur Afirmasi']);

        // Simulasikan kuota afirmasi sudah terisi 25 orang
        $currentCount = Pendaftaran::where('id_jalur', $jalurAfirmasi->id_jalur)
            ->whereIn('status_berkas', ['Diterima', 'Menunggu Verifikasi'])
            ->count();

        $needed = 25 - $currentCount;
        for ($i = 0; $i < $needed; $i++) {
            $otherUser = User::create([
                'email' => 'afirmasi_dummy_'.uniqid().'@example.com',
                'password' => Hash::make('secret'),
                'role' => 'siswa',
            ]);
            $otherSiswa = CalonSiswa::create([
                'id_user' => $otherUser->id_user,
                'nama_lengkap' => 'Dummy Afirmasi '.$i,
                'nisn' => (string) rand(1000000000, 9999999999),
                'asal_sekolah' => 'SMPN 1',
                'no_whatsapp' => '081234567890',
                'tempat_lahir' => 'Badung',
                'tanggal_lahir' => '2008-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Hindu',
                'alamat_domisili' => 'Badung',
            ]);
            Pendaftaran::create([
                'no_registrasi' => 'AF'.str_pad((string) rand(1000, 99999999), 8, '0', STR_PAD_LEFT),
                'id_siswa' => $otherSiswa->id_siswa,
                'id_jurusan' => $jurusan->id_jurusan,
                'id_jalur' => $jalurAfirmasi->id_jalur,
                'id_gelombang' => 1,
                'tgl_daftar' => now()->toDateString(),
                'status_berkas' => 'Menunggu Verifikasi',
            ]);
        }

        // Calon siswa ke-26 mendaftar Jalur Afirmasi -> harus ditolak karena kuota 25 penuh
        $response = $this->post(route('pendaftaran.step2.post'), [
            'nama_bapak' => 'Ayah Tes',
            'pekerjaan_bapak' => 'Karyawan Swasta',
            'nama_ibu' => 'Ibu Tes',
            'pekerjaan_ibu' => 'Wiraswasta',
            'no_wa_ayah' => '081234567891',
            'no_wa_ibu' => '081234567892',
            'alamat_ortu' => 'Badung',
            'id_jurusan' => $jurusan->id_jurusan,
            'id_jalur' => $jalurAfirmasi->id_jalur,
        ]);

        $response->assertSessionHasErrors(['id_jalur']);

        // Clean up dummy afirmasi applicants
        $dummyStudents = CalonSiswa::where('nama_lengkap', 'like', 'Dummy Afirmasi%')->get();
        foreach ($dummyStudents as $ds) {
            Pendaftaran::where('id_siswa', $ds->id_siswa)->delete();
            $uid = $ds->id_user;
            $ds->delete();
            if ($uid) {
                User::where('id_user', $uid)->delete();
            }
        }
        $calonSiswa->delete();
        $user->delete();
    }

    public function test_full_registration_and_financial_syncing_with_voucher_and_sub_jalur(): void
    {
        Storage::fake('public');
        [$user, $calonSiswa] = $this->createSiswaUser();
        $this->actingAs($user);

        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'TJKT (Teknik Jaringan Komputer & Telekomunikasi)'], ['kuota' => 72]);
        $jalurKhusus = JalurPendaftaran::firstOrCreate(['nama_jalur' => 'Jalur Khusus']);
        $gelombangKhusus = GelombangPendaftaran::firstOrCreate(['nama_gelombang' => 'Gelombang Khusus'], [
            'tgl_buka' => '2026-09-07',
            'tgl_tutup' => '2027-01-01',
        ]);

        // 1. Submit Step 2 dengan sub_jalur ojol & voucher rekomendasi
        $responseStep2 = $this->post(route('pendaftaran.step2.post'), [
            'nama_bapak' => 'I Wayan Driver',
            'pekerjaan_bapak' => 'Wiraswasta',
            'nama_ibu' => 'Ni Made Ibu',
            'pekerjaan_ibu' => 'Wiraswasta',
            'no_wa_ayah' => '081234567891',
            'no_wa_ibu' => '081234567892',
            'alamat_ortu' => 'Badung',
            'id_jurusan' => $jurusan->id_jurusan,
            'id_jalur' => $jalurKhusus->id_jalur,
            'sub_jalur' => 'ojol',
            'nama_sub_jalur' => 'Putra/Putri Ojek Online',
            'voucher_jenis' => 'rekomendasi',
            'voucher_kode' => 'ALUMNI2027',
        ]);
        $responseStep2->assertRedirect(route('pendaftaran.step3'));

        // Cek session draft
        $draft = session('pendaftaran_draft');
        $this->assertEquals('ojol', $draft['sub_jalur']);
        $this->assertEquals('rekomendasi', $draft['voucher_jenis']);

        // 2. Submit Step 3 (Unggah Berkas Wajib)
        $foto = UploadedFile::fake()->create('pasfoto.jpg', 500, 'image/jpeg');
        $kk = UploadedFile::fake()->create('kartu_keluarga.pdf', 500, 'application/pdf');
        $akta = UploadedFile::fake()->create('akta_lahir.pdf', 500, 'application/pdf');

        $responseStep3 = $this->post(route('pendaftaran.step3.post'), [
            'berkas_foto' => $foto,
            'berkas_kk' => $kk,
            'berkas_akta' => $akta,
        ]);
        $responseStep3->assertRedirect(route('pendaftaran.step4'));

        // 3. Submit Final Pendaftaran
        $responseSubmit = $this->post(route('pendaftaran.submit'));
        $responseSubmit->assertRedirect(route('pendaftaran.card'));

        $pendaftaran = Pendaftaran::where('id_siswa', $calonSiswa->id_siswa)->first();
        $this->assertNotNull($pendaftaran);
        $this->assertEquals('ojol', $pendaftaran->sub_jalur);
        $this->assertEquals('rekomendasi', $pendaftaran->voucher_jenis);
        $this->assertEquals('ALUMNI2027', $pendaftaran->voucher_kode);

        // 4. Verifikasi Keuangan Siswa otomatis tersinkronisasi
        $keuangan = KeuanganSiswa::where('no_registrasi', $pendaftaran->no_registrasi)->first();
        $this->assertNotNull($keuangan);

        // Math Verification:
        // TI normal: 4.000.000
        // Pendaftaran 150.000: Diskon Voucher 100% -> tagihan pendaftaran Rp 0
        // DPP TI 650.000: Diskon Ojol 50% (1 Thn) -> 325.000
        // SPP TI 650.000: Diskon Ojol 20% + TJKT 10% = 30% -> diskon 195.000 -> SPP Bersih = 455.000
        // Biaya Tetap (MOS 450k + Asuransi 100k + Seragam 1.975k + OSIS 25k) = 2.550.000
        // Total Bersih = 0 + 325.000 + 455.000 + 2.550.000 = 3.330.000
        $billing = app(KeuanganService::class)->calculateStudentBilling($pendaftaran);
        $this->assertEquals(0, $billing['categories']['pendaftaran']['tagihan_bersih']);
        $this->assertEquals(100, $billing['categories']['pendaftaran']['diskon_persen']);
        $this->assertEquals(325000, $billing['categories']['dpp']['tagihan_bersih']);
        $this->assertEquals(50, $billing['categories']['dpp']['diskon_persen']);
        $this->assertEquals(455000, $billing['categories']['spp']['tagihan_bersih']);
        $this->assertEquals(30, $billing['categories']['spp']['diskon_persen']);

        // Minimal Daftar Ulang (Poin 4 Hal 1 PDF):
        // MOS (450k) + Asuransi (100k) + Seragam (1.975k) + DPP Bersih (325k) + SPP Bersih (455k) = 3.305.000
        $this->assertEquals(3305000, $billing['minimal_daftar_ulang']);

        // Clean up test data
        $keuangan->delete();
        $pendaftaran->delete();
        $calonSiswa->delete();
        $user->delete();
    }

    public function test_admin_update_applicant_recalculates_and_syncs_keuangan_automatically(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        [$studentUser, $calonSiswa] = $this->createSiswaUser();
        $jurusanKecantikan = Jurusan::where('nama_jurusan', 'like', '%TKC%')
            ->orWhere('nama_jurusan', 'like', '%Kecantikan%')
            ->first() ?? Jurusan::firstOrCreate(['nama_jurusan' => 'TKC (Tata Kecantikan)'], ['kuota' => 72]);
        $jalurPrestasi = JalurPendaftaran::firstOrCreate(['nama_jalur' => 'Jalur Prestasi']);
        $gelombang1 = GelombangPendaftaran::firstOrCreate(['nama_gelombang' => 'Gelombang 1'], [
            'tgl_buka' => '2027-01-02',
            'tgl_tutup' => '2027-05-11',
        ]);

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => 'ADM'.rand(1000000, 9999999),
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => $jurusanKecantikan->id_jurusan,
            'id_jalur' => $jalurPrestasi->id_jalur,
            'id_gelombang' => $gelombang1->id_gelombang,
            'sub_jalur' => 'prestasi_internasional',
            'nama_sub_jalur' => 'Tingkat Internasional',
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        app(KeuanganService::class)->syncKeuanganStatus($pendaftaran);

        // Awalnya: Kecantikan (SPP dasar 450.000, DPP dasar 500.000)
        // Prestasi Internasional: SPP 25% + Kecantikan 5% = 30% SPP Diskon -> SPP Bersih = 315.000
        $billingInitial = app(KeuanganService::class)->calculateStudentBilling($pendaftaran);
        $this->assertEquals(30, $billingInitial['categories']['spp']['diskon_persen']);
        $this->assertEquals(315000, $billingInitial['categories']['spp']['tagihan_bersih']);

        // Admin mengubah sub_jalur menjadi anak_guru (SPP 70% + Kecantikan 5% = 75%)
        $response = $this->postJson(route('admin.pendaftaran.data.update', ['no_registrasi' => $pendaftaran->no_registrasi]), [
            'nama_lengkap' => $calonSiswa->nama_lengkap,
            'nisn' => $calonSiswa->nisn,
            'no_whatsapp' => $calonSiswa->no_whatsapp,
            'tempat_lahir' => $calonSiswa->tempat_lahir,
            'tanggal_lahir' => $calonSiswa->tanggal_lahir,
            'jenis_kelamin' => $calonSiswa->jenis_kelamin,
            'agama' => $calonSiswa->agama,
            'asal_sekolah' => $calonSiswa->asal_sekolah,
            'alamat_domisili' => $calonSiswa->alamat_domisili,
            'id_jurusan' => $jurusanKecantikan->id_jurusan,
            'id_jalur' => $jalurPrestasi->id_jalur,
            'id_gelombang' => $gelombang1->id_gelombang,
            'sub_jalur' => 'anak_guru',
            'nama_sub_jalur' => 'Anak Kandung Guru/Staf',
            'status_berkas' => 'Diterima',
            'tgl_daftar' => $pendaftaran->tgl_daftar,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi database & recalculation
        $pendaftaran->refresh();
        $this->assertEquals('anak_guru', $pendaftaran->sub_jalur);
        $this->assertEquals('Diterima', $pendaftaran->status_berkas);

        $billingUpdated = app(KeuanganService::class)->calculateStudentBilling($pendaftaran);
        $this->assertEquals(75, $billingUpdated['categories']['spp']['diskon_persen']);
        $this->assertEquals(112500, $billingUpdated['categories']['spp']['tagihan_bersih']);

        // Bersihkan data tes agar tidak mencemari database
        $pendaftaran->keuangan?->delete();
        $pendaftaran->delete();
        $calonSiswa->delete();
        $studentUser->delete();
        $admin->panitia?->delete();
        $admin->delete();
    }

    public function test_step2_to_step3_flow_loads_documents_without_parameter_error(): void
    {
        [$user, $calonSiswa] = $this->createSiswaUser();

        $jurusan = Jurusan::first() ?? Jurusan::create([
            'nama_jurusan' => 'PPLG',
            'kuota' => 100,
            'spp_bulanan' => 450000,
            'dpp_pangkal' => 500000,
        ]);

        $jalur = JalurPendaftaran::first() ?? JalurPendaftaran::create([
            'nama_jalur' => 'Jalur Prestasi',
            'deskripsi' => 'Prestasi',
            'diskon_persen' => 25,
            'aktif' => true,
        ]);

        $gelombang = GelombangPendaftaran::first() ?? GelombangPendaftaran::create([
            'nama_gelombang' => 'Gelombang Khusus',
            'tgl_buka' => '2026-01-01',
            'tgl_tutup' => '2027-12-31',
            'biaya_daftar' => 0,
            'aktif' => true,
        ]);

        // Simpan step 1 session draft
        $this->actingAs($user)->withSession([
            'pendaftaran_draft' => [
                'id_jurusan' => $jurusan->id_jurusan,
                'id_jalur' => $jalur->id_jalur,
                'id_gelombang' => $gelombang->id_gelombang,
                'sub_jalur' => 'prestasi_prov_nas',
                'voucher_jenis' => null,
            ],
        ]);

        // Kirim Step 2
        $response = $this->actingAs($user)->post(route('pendaftaran.step2.post'), [
            'id_jurusan' => $jurusan->id_jurusan,
            'id_jalur' => $jalur->id_jalur,
            'sub_jalur' => 'prestasi_prov_nas',
            'nama_bapak' => 'Bapak Uji',
            'pekerjaan_bapak' => 'PNS',
            'nama_ibu' => 'Ibu Uji',
            'pekerjaan_ibu' => 'Guru',
            'no_wa_ayah' => '081234567891',
            'no_wa_ibu' => '081234567892',
            'alamat_ortu' => 'Jl. Uji No. 123',
        ]);

        $response->assertRedirect(route('pendaftaran.step3'));

        // Akses Step 3 (ini yang sebelumnya error: Unknown named parameter $jalurName)
        $step3Res = $this->actingAs($user)->get(route('pendaftaran.step3'));
        $step3Res->assertOk();
        $step3Res->assertViewIs('registration.step-3');
        $step3Res->assertViewHas('requiredDocs');
    }
}
