<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessControlFeatureTest extends TestCase
{
    public function test_logged_in_admin_is_redirected_to_admin_dashboard_when_visiting_home(): void
    {
        $admin = User::create([
            'email' => 'admin_home_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('home'));

        $response->assertRedirect(route('admin.dashboard'));

        $admin->delete();
    }

    public function test_logged_in_admin_cannot_access_landing_info_and_auth_pages(): void
    {
        $admin = User::create([
            'email' => 'admin_pages_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->get(route('syarat-ketentuan'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('kebijakan-privasi'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('login'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('register'))->assertRedirect(route('admin.dashboard'));
        $this->get('/sign')->assertRedirect(route('admin.dashboard'));

        $admin->delete();
    }

    public function test_logged_in_admin_cannot_access_student_registration_pages(): void
    {
        $admin = User::create([
            'email' => 'admin_student_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->get(route('pendaftaran.step1'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('pendaftaran.step2'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('pendaftaran.step3'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('pendaftaran.step4'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('pendaftaran.card'))->assertRedirect(route('admin.dashboard'));

        $admin->delete();
    }

    public function test_guest_and_student_can_still_access_home_landing_page(): void
    {
        $guestResponse = $this->get(route('home'));
        $guestResponse->assertStatus(200);

        $student = User::create([
            'email' => 'student_home_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);
        CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Student Test',
            'no_whatsapp' => '081234567890',
        ]);

        $studentResponse = $this->actingAs($student)->get(route('home'));
        $studentResponse->assertStatus(200);

        $student->calonSiswa()->delete();
        $student->delete();
    }

    public function test_admin_login_redirects_directly_to_admin_dashboard(): void
    {
        $email = 'admin_login_'.uniqid().'@example.com';
        $admin = User::create([
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $admin->delete();
    }

    public function test_admin_can_access_beranda_page(): void
    {
        $admin = User::create([
            'email' => 'admin_beranda_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.beranda'));

        $response->assertOk();
        $response->assertSee('section-beranda', false);
        $response->assertSee('ADMIN SMK TI BALI GLOBAL BADUNG');
        $response->assertSee('admin@smktibadung.sch.id');
        $response->assertSee('NPSN Sekolah');
        $response->assertSee('69959273');
        $response->assertSee('Akreditasi Sekolah');
        $response->assertSee('Jenjang Sekolah');
        $response->assertSee('Status Sekolah');
        $response->assertSee('Distribusi Peminat Jurusan', false);
        $response->assertSee('Kuota SPMB');
        $response->assertSee('donut-chart-svg', false);

        $admin->delete();
    }

    public function test_admin_can_poll_the_applicant_table_fingerprint(): void
    {
        $admin = User::create([
            'email' => 'admin_table_poll_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.tabel-jurusan'))
            ->assertOk()
            ->assertSee('id="section-tabel-jurusan"', false)
            ->assertSee('data-registration-fingerprint=', false);

        $response = $this->actingAs($admin)->getJson(route('admin.pendaftaran.poll'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertHeader('Cache-Control', 'no-store, private');

        $fingerprint = $response->json('fingerprint');
        $this->assertIsString($fingerprint);
        $this->assertSame(64, strlen($fingerprint));

        $admin->delete();
    }

    public function test_admin_can_access_daftar_ulang_page(): void
    {
        $admin = User::create([
            'email' => 'admin_daftar_ulang_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.daftar-ulang'));

        $response->assertOk();
        $response->assertSee('section-daftar-ulang', false);
        $response->assertSee('Sudah Daftar Ulang');
        $response->assertSee('Menunggu');
        $response->assertSee('du-table-body', false);
        $response->assertSee('Ekspor Excel');

        $admin->delete();
    }

    public function test_admin_can_update_daftar_ulang_status(): void
    {
        $admin = User::create([
            'email' => 'admin_status_du_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $studentUser = User::create([
            'email' => 'student_du_status_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $studentUser->id_user,
            'nama_lengkap' => 'Siswa Tes Status DU',
            'nisn' => '99'.random_int(10000000, 99999999),
            'no_whatsapp' => '081234567888',
        ]);

        $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $noReg = str_pad((string) ($lastRegistrationNumber + 1), 3, '0', STR_PAD_LEFT);

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now(),
            'status_berkas' => 'Diterima',
        ]);

        try {
            $response = $this->actingAs($admin)->postJson(route('admin.daftar-ulang.status'), [
                'no_registrasi' => $pendaftaran->no_registrasi,
                'status' => 'Sudah Daftar Ulang',
            ]);

            $response->assertOk();
            $response->assertJson([
                'success' => true,
                'no_registrasi' => $pendaftaran->no_registrasi,
                'status' => 'Sudah Daftar Ulang',
            ]);
        } finally {
            $pendaftaran->delete();
            $calonSiswa->delete();
            $studentUser->delete();
            $admin->delete();
        }
    }

    public function test_daftar_ulang_only_includes_verified_students_and_excludes_rejected(): void
    {
        $admin = User::create([
            'email' => 'admin_du_filter_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $studentUser = User::create([
            'email' => 'student_du_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $studentUser->id_user,
            'nama_lengkap' => 'Siswa Tes Daftar Ulang',
            'nisn' => '99'.random_int(10000000, 99999999),
            'tempat_lahir' => 'Badung',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'asal_sekolah' => 'SMPN 1 Kuta',
            'alamat_domisili' => 'Kuta',
            'no_whatsapp' => '081234567899',
        ]);

        $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $noReg = (string) ($lastRegistrationNumber + 1);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = (string) ((int) $noReg + 1);
        }

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now(),
            'status_berkas' => 'Diterima',
        ]);

        try {
            // 1. Siswa yang berstatus Diterima harus muncul di daftar ulang
            $response = $this->actingAs($admin)->get(route('admin.daftar-ulang'));
            $response->assertOk();
            $response->assertSee('du-row-'.$pendaftaran->no_registrasi, false);

            // 2. Tolak verifikasi siswa via AJAX
            $verifResponse = $this->actingAs($admin)->postJson(route('admin.verifikasi', $pendaftaran->no_registrasi), [
                'status_akhir' => 'Ditolak',
                'catatan_verifikator' => 'Berkas tidak memenuhi syarat.',
            ]);
            $verifResponse->assertOk();
            $verifResponse->assertJson([
                'success' => true,
                'status_akhir' => 'Ditolak',
            ]);

            // 3. Setelah ditolak, siswa HARUS HILANG dari daftar ulang
            $responseAfterReject = $this->actingAs($admin)->get(route('admin.daftar-ulang'));
            $responseAfterReject->assertOk();
            $responseAfterReject->assertDontSee('du-row-'.$pendaftaran->no_registrasi, false);
        } finally {
            VerifikasiPanitia::where('no_registrasi', $pendaftaran->no_registrasi)->delete();
            $pendaftaran->delete();
            $calonSiswa->delete();
            $studentUser->delete();
            $admin->delete();
        }
    }

    public function test_status_berkas_renders_six_cards_and_tabel_pendaftar_uses_terverifikasi(): void
    {
        $admin = User::create([
            'email' => 'admin_six_cards_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.status-berkas'));
        $response->assertOk();

        // 6 Kartu KPI Status Berkas
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Menunggu');
        $response->assertSee('Terverifikasi');
        $response->assertSee('Perlu Revisi');
        $response->assertSee('Ditolak');
        $response->assertSee('Diterima');
        $response->assertSee('kpi-total', false);
        $response->assertSee('kpi-pending', false);
        $response->assertSee('kpi-verified', false);
        $response->assertSee('kpi-revision', false);
        $response->assertSee('kpi-rejected', false);
        $response->assertSee('kpi-accepted', false);

        // Tabel Pendaftar menggunakan 'Terverifikasi'
        $tableResponse = $this->actingAs($admin)->get(route('admin.tabel-jurusan'));
        $tableResponse->assertOk();
        $tableResponse->assertSee('data-filter-label="Terverifikasi"', false);

        $admin->delete();
    }
}
