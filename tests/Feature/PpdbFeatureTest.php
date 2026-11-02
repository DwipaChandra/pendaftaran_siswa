<?php

namespace Tests\Feature;

use App\Models\BerkasPersyaratan;
use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use App\Services\KeuanganService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbFeatureTest extends TestCase
{
    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('SMK TI Bali Global Badung');
        $response->assertSee('TKC (Tata Kecantikan)');
        $response->assertSee('Pertanyaan yang Sering Ditanyakan');
        $response->assertSee('Amankan');
        $response->assertSee('Kuota Program Keahlian');
        $response->assertSee('family=DM+Sans', false);
        $response->assertSee('family=Bricolage+Grotesque', false);
        $response->assertSee('color: #141414;', false);
        $response->assertSee('target="_blank" rel="noopener noreferrer"', false);
        $response->assertSee('hover:translate-x-1 hover:text-sky-300', false);
        $response->assertSee('href="https://www.smktibaliglobalbadung.sch.id/jurusan/pplg"', false);
        $response->assertSee('href="https://www.smktibaliglobalbadung.sch.id/jurusan/tkjt"', false);
        $response->assertSee('href="https://www.smktibaliglobalbadung.sch.id/jurusan/dkv"', false);
        $response->assertSee('href="https://www.smktibaliglobalbadung.sch.id/jurusan/bisnis-digital"', false);
        $response->assertSee('href="https://www.smktibaliglobalbadung.sch.id/jurusan/tkc"', false);
        $response->assertSee('href="#section-faq"', false);
        $response->assertSee(route('kebijakan-privasi'));
        $response->assertSee(route('syarat-ketentuan'));
        $response->assertSee('<details', false);
        $response->assertSee('Bagaimana cara mendaftar secara online?');
    }

    public function test_quota_status_returns_consistent_progress_and_status_data(): void
    {
        $response = $this->getJson(route('home.quota-status'));

        $response->assertOk()->assertJsonStructure([
            'jurusan' => [
                '*' => ['id', 'nama_jurusan', 'kuota', 'terisi', 'tersisa', 'persentase', 'warna', 'status'],
            ],
        ]);

        foreach ($response->json('jurusan') as $jurusan) {
            $expectedPercentage = min(100, (int) round(($jurusan['terisi'] / $jurusan['kuota']) * 100));
            $expectedColor = $expectedPercentage >= 80 ? 'red' : ($expectedPercentage >= 50 ? 'yellow' : 'green');

            $this->assertSame($expectedPercentage, $jurusan['persentase']);
            $this->assertSame($expectedColor, $jurusan['warna']);
            $this->assertSame(max(0, $jurusan['kuota'] - $jurusan['terisi']), $jurusan['tersisa']);
        }
    }

    public function test_quota_counts_only_accepted_applicants_as_filled_seats(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Jurusan Tes Status '.uniqid(),
            'kuota' => 72,
        ]);
        $acceptedCountBefore = 0;
        $users = [];
        $calonSiswaRecords = [];
        $registrations = [];

        try {
            foreach (['Menunggu Verifikasi', 'Diterima'] as $status) {
                $user = User::create([
                    'email' => 'quota_status_'.uniqid().'@example.com',
                    'password' => bcrypt('password123'),
                    'role' => 'siswa',
                ]);
                $users[] = $user;

                $calonSiswa = CalonSiswa::create([
                    'id_user' => $user->id_user,
                    'nama_lengkap' => 'Siswa Tes Kuota',
                    'no_whatsapp' => '081234567890',
                ]);
                $calonSiswaRecords[] = $calonSiswa;

                $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
                    ->filter(fn ($number) => ctype_digit((string) $number))
                    ->map(fn ($number) => (int) $number)
                    ->max() ?? 0;
                $registrationNumber = str_pad((string) ($lastRegistrationNumber + 1), 3, '0', STR_PAD_LEFT);
                while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
                    $registrationNumber = str_pad((string) ((int) $registrationNumber + 1), 3, '0', STR_PAD_LEFT);
                }

                $registrations[] = Pendaftaran::create([
                    'no_registrasi' => $registrationNumber,
                    'id_siswa' => $calonSiswa->id_siswa,
                    'id_jurusan' => $jurusan->id_jurusan,
                    'id_jalur' => 1,
                    'id_gelombang' => 1,
                    'tgl_daftar' => now()->toDateString(),
                    'status_berkas' => $status,
                ]);
            }

            $jurusanQuota = collect($this->get(route('home.quota-status'))
                ->assertOk()
                ->json('jurusan'))
                ->firstWhere('id', (int) $jurusan->id_jurusan);

            $expectedAcceptedCount = $acceptedCountBefore + 1;
            $this->assertSame($expectedAcceptedCount, $jurusanQuota['terisi']);
            $this->assertSame(max(0, (int) $jurusan->kuota - $expectedAcceptedCount), $jurusanQuota['tersisa']);
        } finally {
            foreach ($registrations as $registration) {
                $registration->delete();
            }

            foreach ($calonSiswaRecords as $calonSiswa) {
                $calonSiswa->delete();
            }

            foreach ($users as $user) {
                $user->delete();
            }

            $jurusan->delete();
        }
    }

    public function test_student_dropdown_links_to_registration_card_after_submission(): void
    {
        $student = User::create([
            'email' => 'student_dropdown_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = null;
        $pendaftaran = null;
        $panitiaUser = null;
        $panitia = null;
        $registrationNumber = null;

        try {
            $this->actingAs($student)
                ->get(route('home'))
                ->assertOk()
                ->assertSee('Formulir Pendaftaran')
                ->assertSee(route('pendaftaran.step1'));

            $calonSiswa = CalonSiswa::create([
                'id_user' => $student->id_user,
                'nama_lengkap' => 'Siswa Menu Tes',
                'no_whatsapp' => '081234567890',
                'alamat_domisili' => 'Alamat rumah siswa tes',
                'alamat_ortu' => 'Alamat rumah orang tua tes',
                'nama_bapak' => 'Ayah Menu Tes',
                'pekerjaan_bapak' => 'Guru',
                'nama_ibu' => 'Ibu Menu Tes',
                'pekerjaan_ibu' => 'Wiraswasta',
            ]);
            $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
                ->filter(fn ($number) => ctype_digit((string) $number))
                ->map(fn ($number) => (int) $number)
                ->max() ?? 0;
            $registrationNumber = str_pad((string) ($lastRegistrationNumber + 1), 3, '0', STR_PAD_LEFT);
            while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
                $registrationNumber = str_pad((string) ((int) $registrationNumber + 1), 3, '0', STR_PAD_LEFT);
            }

            $pendaftaran = Pendaftaran::create([
                'no_registrasi' => $registrationNumber,
                'id_siswa' => $calonSiswa->id_siswa,
                'id_jurusan' => 1,
                'id_jalur' => 1,
                'id_gelombang' => 1,
                'tgl_daftar' => now()->toDateString(),
                'status_berkas' => 'Menunggu Verifikasi',
            ]);

            $panitiaUser = User::create([
                'email' => 'panitia_status_'.uniqid().'@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]);
            $panitia = Panitia::create([
                'id_user' => $panitiaUser->id_user,
                'nama_panitia' => 'Panitia Tes Status',
                'nip' => 'STS'.random_int(10000, 99999),
                'no_telepon' => '081234567890',
                'bidang_studi' => 'SPMB',
            ]);

            $student->unsetRelation('calonSiswa');
            $registeredHomeResponse = $this->actingAs($student)->get(route('home'));
            $registeredHomeResponse->assertOk()
                ->assertSee('CEK STATUS')
                ->assertDontSee('w-2 h-2 shrink-0 rounded-full bg-blue-600 animate-pulse', false);
            $this->assertStringContainsString('/pendaftaran/kartu', $registeredHomeResponse->getContent());

            $this->actingAs($student)
                ->get(route('pendaftaran.card'))
                ->assertOk()
                ->assertSee('Email Siswa')
                ->assertSee($student->email)
                ->assertSee('Alamat Siswa')
                ->assertSee('Alamat rumah siswa tes')
                ->assertSee('Alamat Orang Tua')
                ->assertSee('Alamat rumah orang tua tes')
                ->assertSee('Nama Ayah')
                ->assertSee('Ayah Menu Tes')
                ->assertSee('Pekerjaan Ayah')
                ->assertSee('Guru')
                ->assertSee('Nama Ibu')
                ->assertSee('Ibu Menu Tes')
                ->assertSee('Pekerjaan Ibu')
                ->assertSee('Wiraswasta')
                ->assertSeeInOrder([
                    'id="page-header-banner"',
                    'id="application-status"',
                    'id="printable-id-card"',
                ], false);

            $statusHeadings = [
                'Menunggu Verifikasi' => 'Berkasmu Sedang Diverifikasi',
                'Perbaikan Berkas' => 'Ada Berkas yang Perlu Diperbaiki',
                'Diterima' => 'Selamat, Siswa Menu Tes! Anda DITERIMA',
                'Ditolak' => 'Status Pendaftaran: Ditolak',
            ];

            foreach ($statusHeadings as $status => $heading) {
                $pendaftaran->update(['status_berkas' => $status]);

                $verificationNote = 'Catatan resmi untuk status '.$status.'.';
                if ($status === 'Menunggu Verifikasi') {
                    VerifikasiPanitia::where('no_registrasi', $registrationNumber)->delete();
                } else {
                    VerifikasiPanitia::updateOrCreate(
                        ['no_registrasi' => $registrationNumber],
                        [
                            'id_panitia' => $panitia->id_panitia,
                            'tgl_verifikasi' => now(),
                            'catatan_verifikator' => $verificationNote,
                            'status_akhir' => $status,
                        ]
                    );
                }

                $cardResponse = $this->actingAs($student)
                    ->get(route('pendaftaran.card'))
                    ->assertOk()
                    ->assertSee('id="application-status"', false)
                    ->assertSee($heading);

                if ($status === 'Diterima') {
                    $cardResponse->assertSee('Catatan Resmi Verifikator')
                        ->assertSee($verificationNote)
                        ->assertSee('Alamat Sekolah')
                        ->assertSee('Kontak Panitia')
                        ->assertSee('Masuk Grup Calon Siswa')
                        ->assertSee('Masuk grup')
                        ->assertDontSee('Hubungi panitia')
                        ->assertDontSee('Konsultasi status pendaftaran')
                        ->assertDontSee('Konfirmasi jadwal dan persyaratan daftar ulang sebelum datang ke sekolah.')
                        ->assertSee('Langkah selanjutnya', false)
                        ->assertSee('Chat Panitia via WhatsApp')
                        ->assertSeeInOrder([
                            'Alamat Sekolah',
                            'Kontak Panitia',
                            'Catatan Resmi Verifikator',
                        ]);
                } elseif ($status === 'Menunggu Verifikasi') {
                    $cardResponse->assertDontSee('Catatan Resmi Verifikator')
                        ->assertDontSee('Alamat Sekolah')
                        ->assertDontSee('Kontak Panitia')
                        ->assertDontSee('Langkah selanjutnya', false)
                        ->assertDontSee('Chat Panitia via WhatsApp');
                } else {
                    $cardResponse->assertSee('Catatan Resmi Verifikator')
                        ->assertSee($verificationNote)
                        ->assertDontSee('Alamat Sekolah')
                        ->assertDontSee('Kontak Panitia')
                        ->assertDontSee('Langkah selanjutnya', false)
                        ->assertDontSee('Chat Panitia via WhatsApp');
                }
            }
        } finally {
            if ($registrationNumber !== null) {
                VerifikasiPanitia::where('no_registrasi', $registrationNumber)->delete();
            }

            $pendaftaran?->delete();
            $calonSiswa?->delete();
            $panitia?->delete();
            $panitiaUser?->delete();
            $student->delete();
        }
    }

    public function test_login_and_register_pages_can_be_rendered(): void
    {
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Masuk');
        $loginRes->assertSee('input:-webkit-autofill:hover', false);

        $registerRes = $this->get(route('register'));
        $registerRes->assertStatus(200);
        $registerRes->assertSee('Daftar Akun');
        $registerRes->assertSee('input:-webkit-autofill:hover', false);
        $registerRes->assertSee('hover:border-slate-300', false);
        $registerRes->assertSee('KONFIRMASI PASSWORD');
    }

    public function test_legal_pages_can_be_rendered(): void
    {
        $this->get(route('syarat-ketentuan'))->assertStatus(200);
        $this->get(route('kebijakan-privasi'))->assertStatus(200);
    }

    public function test_registration_rejects_invalid_whatsapp_number(): void
    {
        $testEmail = 'invalid_whatsapp_'.time().'@example.com';

        $response = $this->post(route('register.post'), [
            'nama_lengkap' => 'Calon Siswa Pengujian',
            'email' => $testEmail,
            'no_whatsapp' => '0812ABC',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('no_whatsapp');
        $this->assertDatabaseMissing('users', ['email' => $testEmail]);
    }

    public function test_registration_accepts_whatsapp_number_with_plus_prefix(): void
    {
        $testEmail = 'short_whatsapp_'.time().'@example.com';

        $response = $this->post(route('register.post'), [
            'nama_lengkap' => 'Calon Siswa Pengujian',
            'email' => $testEmail,
            'no_whatsapp' => '+123',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('calon_siswa', ['no_whatsapp' => '+123']);
    }

    public function test_step_one_accepts_whatsapp_plus_prefix_and_rejects_other_characters(): void
    {
        $user = User::create([
            'email' => 'step_one_whatsapp_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $payload = [
            'nama_lengkap' => 'Calon Siswa Pengujian',
            'nisn' => '0012345678',
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'asal_sekolah' => 'SMP Negeri 1 Denpasar',
            'alamat_domisili' => 'Jl. Pengujian No. 1',
            'no_whatsapp' => '+6281234567890',
        ];

        try {
            $this->actingAs($user)
                ->post(route('pendaftaran.step1.post'), $payload)
                ->assertRedirect(route('pendaftaran.step2'));

            $this->assertDatabaseHas('calon_siswa', [
                'id_user' => $user->id_user,
                'nisn' => '0012345678',
                'no_whatsapp' => '+6281234567890',
            ]);

            $payload['no_whatsapp'] = '0812ABC';

            $this->actingAs($user)
                ->post(route('pendaftaran.step1.post'), $payload)
                ->assertSessionHasErrors('no_whatsapp');

            $payload['no_whatsapp'] = '+6281234567890';
            $payload['nisn'] = '123';

            $this->actingAs($user)
                ->post(route('pendaftaran.step1.post'), $payload)
                ->assertSessionHasErrors('nisn');
        } finally {
            CalonSiswa::where('id_user', $user->id_user)->delete();
            $user->delete();
        }
    }

    public function test_registration_rejects_password_confirmation_mismatch(): void
    {
        $testEmail = 'password_confirmation_'.time().'@example.com';

        $response = $this->post(route('register.post'), [
            'nama_lengkap' => 'Calon Siswa Pengujian',
            'email' => $testEmail,
            'no_whatsapp' => '081299887766',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => $testEmail]);
    }

    public function test_new_registration_uses_wita_date_and_three_digit_sequence(): void
    {
        $testEmail = 'simple_registration_'.time().'@example.com';
        $lastSimpleNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $expectedNumber = str_pad((string) ($lastSimpleNumber + 1), 3, '0', STR_PAD_LEFT);
        $user = User::create([
            'email' => $testEmail,
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        Carbon::setTestNow(Carbon::parse('2026-10-22 17:30:00', 'UTC'));

        try {
            $response = $this->actingAs($user)->withSession([
                'pendaftaran_draft' => [
                    'id_jurusan' => 1,
                    'id_jalur' => 1,
                    'id_gelombang' => 1,
                ],
                'berkas_draft' => $this->completeDocumentDraft(),
            ])->post(route('pendaftaran.submit'));

            $response->assertRedirect(route('pendaftaran.card'));
            $calonSiswa = CalonSiswa::where('id_user', $user->id_user)->firstOrFail();
            $pendaftaran = Pendaftaran::where('id_siswa', $calonSiswa->id_siswa)->firstOrFail();

            $this->assertSame($expectedNumber, $pendaftaran->no_registrasi);
            $this->assertSame('2026-10-23', $pendaftaran->tgl_daftar->toDateString());
        } finally {
            $calonSiswa = CalonSiswa::where('id_user', $user->id_user)->first();
            if ($calonSiswa) {
                $calonSiswa->pendaftaran()->delete();
                $calonSiswa->delete();
            }

            $user->delete();
            Carbon::setTestNow();
        }
    }

    public function test_guest_cannot_access_student_form_steps(): void
    {
        $response = $this->get(route('pendaftaran.step1'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_panel(): void
    {
        $studentUser = User::where('role', 'siswa')->first();
        if ($studentUser) {
            $response = $this->actingAs($studentUser)->get(route('admin.dashboard'));
            $response->assertRedirect(route('home'));
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_student_cannot_delete_registration(): void
    {
        $student = User::create([
            'email' => 'student_delete_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $response = $this->actingAs($student)->delete(route('admin.pendaftaran.destroy', [
            'no_registrasi' => 'REG-NOT-FOUND',
        ]));

        $response->assertRedirect(route('home'));
        $student->delete();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $response = $this->actingAs($adminUser)
                ->withSession(['success' => 'Selamat datang di Dasbor Panitia.'])
                ->get(route('admin.dashboard'));
            $response->assertStatus(200);
            $response->assertSee('Dasbor Administrasi SPMB');
            $response->assertSee('admin-sidebar', false);
            $response->assertSee('admin-top-sidebar-toggle', false);
            $response->assertSee('section-status-berkas', false);
            $response->assertSee('section-kuota-rombel', false);
            $response->assertSee('section-tabel-jurusan', false);
            $response->assertSee(route('admin.password.edit'));
            $response->assertSee('drawer-docs-list', false);
            $response->assertSee('Ganti File');
            $response->assertSee('drawer-edit-mode', false);
            $response->assertSee('TKC (Tata Kecantikan)');
            $response->assertDontSee('Selamat datang di Dasbor Panitia.');
            $response->assertDontSee('Semua Dasbor');
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_admin_can_access_separated_admin_pages(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $this->actingAs($adminUser)
                ->get(route('admin.status-berkas'))
                ->assertOk()
                ->assertDontSee('Semua Dasbor')
                ->assertDontSee('Halaman 1 / 3')
                ->assertSee('Status Berkas');

            $this->actingAs($adminUser)
                ->get(route('admin.kuota-rombel'))
                ->assertOk()
                ->assertDontSee('Semua Dasbor')
                ->assertDontSee('Halaman 2 / 3')
                ->assertSee('Kuota Rombel');

            $this->actingAs($adminUser)
                ->get(route('admin.tabel-jurusan'))
                ->assertOk()
                ->assertDontSee('Semua Dasbor')
                ->assertDontSee('Halaman 3 / 3')
                ->assertSee('Tabel Pendaftar Jurusan');

            $this->actingAs($adminUser)
                ->get(route('admin.office'))
                ->assertOk()
                ->assertSee('Office Administrasi')
                ->assertSee('Gelombang Pendaftaran')
                ->assertSee('Ekspor Data Pendaftar')
                ->assertSee('admin-wave-select');
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_admin_can_delete_selected_registrations_without_deleting_student_accounts(): void
    {
        $admin = User::create([
            'email' => 'admin_bulk_delete_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $studentRecords = [];
        $calonSiswaRecords = [];
        $registrationNumbers = [];
        $nextNumber = (Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0) + 1;

        for ($index = 0; $index < 3; $index++) {
            $studentRecords[] = User::create([
                'email' => 'student_bulk_delete_'.$index.'_'.uniqid().'@example.com',
                'password' => bcrypt('password123'),
                'role' => 'siswa',
            ]);
            $calonSiswaRecords[] = CalonSiswa::create([
                'id_user' => $studentRecords[$index]->id_user,
                'nama_lengkap' => 'Siswa Hapus Massal '.$index,
                'no_whatsapp' => '08123456789'.$index,
            ]);

            $registrationNumber = str_pad((string) $nextNumber++, 3, '0', STR_PAD_LEFT);
            while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
                $registrationNumber = str_pad((string) $nextNumber++, 3, '0', STR_PAD_LEFT);
            }
            $registrationNumbers[] = $registrationNumber;
            Pendaftaran::create([
                'no_registrasi' => $registrationNumber,
                'id_siswa' => $calonSiswaRecords[$index]->id_siswa,
                'id_jurusan' => 1,
                'id_jalur' => 1,
                'id_gelombang' => 1,
                'tgl_daftar' => '2026-01-0'.($index + 1),
                'status_berkas' => 'Menunggu Verifikasi',
            ]);
        }

        try {
            $selectedNumbers = array_slice($registrationNumbers, 0, 2);
            $this->actingAs($admin)
                ->post(route('admin.pendaftaran.destroy-selected'), ['no_registrasi' => $selectedNumbers])
                ->assertRedirect(route('admin.dashboard'))
                ->assertSessionHas('success', '2 pendaftaran terpilih berhasil dihapus.');

            foreach ($selectedNumbers as $registrationNumber) {
                $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $registrationNumber]);
            }
            $this->assertDatabaseHas('pendaftaran', ['no_registrasi' => $registrationNumbers[2]]);

            foreach ($studentRecords as $student) {
                $this->assertDatabaseHas('users', ['id_user' => $student->id_user]);
            }
        } finally {
            Pendaftaran::whereIn('no_registrasi', $registrationNumbers)->delete();
            foreach ($calonSiswaRecords as $calonSiswa) {
                $calonSiswa->delete();
            }
            foreach ($studentRecords as $student) {
                $student->delete();
            }
            $admin->delete();
        }
    }

    public function test_admin_can_replace_applicant_document_and_keep_other_documents(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'email' => 'admin_documents_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $student = User::create([
            'email' => 'student_documents_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Tes Dokumen',
            'no_whatsapp' => '081234567890',
        ]);
        $lastSimpleNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $registrationNumber = str_pad((string) ($lastSimpleNumber + 1), 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
            $registrationNumber = str_pad((string) ((int) $registrationNumber + 1), 3, '0', STR_PAD_LEFT);
        }
        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $registrationNumber,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        Storage::disk('public')->put('berkas/old-kk.pdf', 'old kk');
        Storage::disk('public')->put('berkas/photo.png', 'photo');
        BerkasPersyaratan::create([
            'no_registrasi' => $registrationNumber,
            'jenis_berkas' => 'Kartu Keluarga (KK)',
            'nama_file' => 'kk-lama.pdf',
            'ukuran_file' => 6,
            'path_file' => 'storage/berkas/old-kk.pdf',
        ]);
        BerkasPersyaratan::create([
            'no_registrasi' => $registrationNumber,
            'jenis_berkas' => 'Pasfoto Formal',
            'nama_file' => 'foto.png',
            'ukuran_file' => 5,
            'path_file' => 'storage/berkas/photo.png',
        ]);
        $this->assertDatabaseHas('pendaftaran', ['no_registrasi' => $registrationNumber]);

        try {
            $uploadResponse = $this->actingAs($admin)
                ->postJson(route('admin.berkas.update', $registrationNumber), [
                    'berkas_kk' => UploadedFile::fake()->create('kk-baru.pdf', 100, 'application/pdf'),
                ])
                ->assertOk()
                ->assertJsonPath('success', true);

            $updatedKk = BerkasPersyaratan::where('no_registrasi', $registrationNumber)
                ->where('jenis_berkas', 'Kartu Keluarga (KK)')
                ->firstOrFail();

            $this->assertSame('kk-baru.pdf', $updatedKk->nama_file);
            $this->assertFileExists(Storage::disk('public')->path('berkas/old-kk.pdf'));
            $this->assertFileExists(Storage::disk('public')->path('berkas/photo.png'));

            $newFilePath = substr($updatedKk->path_file, strlen('storage/'));
            $undoResponse = $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $uploadResponse->json('undo_token')])
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['reverse_token']);

            $this->assertDatabaseHas('berkas_persyaratan', [
                'no_registrasi' => $registrationNumber,
                'jenis_berkas' => 'Kartu Keluarga (KK)',
                'nama_file' => 'kk-lama.pdf',
                'path_file' => 'storage/berkas/old-kk.pdf',
            ]);
            $this->assertFileExists(Storage::disk('public')->path('berkas/old-kk.pdf'));
            $this->assertFileExists(Storage::disk('public')->path($newFilePath));

            $redoResponse = $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $undoResponse->json('reverse_token')])
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['reverse_token']);
            $this->assertDatabaseHas('berkas_persyaratan', [
                'no_registrasi' => $registrationNumber,
                'jenis_berkas' => 'Kartu Keluarga (KK)',
                'nama_file' => 'kk-baru.pdf',
                'path_file' => 'storage/'.$newFilePath,
            ]);

            $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $redoResponse->json('reverse_token')])
                ->assertOk();

            $this->actingAs($admin)
                ->postJson(route('admin.berkas.update', $registrationNumber), [
                    'berkas_kk' => UploadedFile::fake()->create('invalid.txt', 10, 'text/plain'),
                ])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('berkas_kk');
        } finally {
            $filePaths = BerkasPersyaratan::where('no_registrasi', $registrationNumber)
                ->pluck('path_file')
                ->map(fn (string $path): string => substr($path, strlen('storage/')))
                ->all();
            BerkasPersyaratan::where('no_registrasi', $registrationNumber)->delete();
            Storage::disk('public')->delete(array_merge($filePaths, ['berkas/old-kk.pdf'], isset($newFilePath) ? [$newFilePath] : []));
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
            $admin->delete();
        }
    }

    public function test_admin_can_save_and_undo_applicant_data_changes(): void
    {
        $admin = User::create([
            'email' => 'admin_profile_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $studentEmail = 'student_profile_'.time().'@example.com';
        $student = User::create([
            'email' => $studentEmail,
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Nama Sebelum Edit',
            'no_whatsapp' => '081234567890',
            'alamat_domisili' => 'Alamat Lama',
        ]);
        $lastSimpleNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $registrationNumber = str_pad((string) ($lastSimpleNumber + 1), 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
            $registrationNumber = str_pad((string) ((int) $registrationNumber + 1), 3, '0', STR_PAD_LEFT);
        }
        $registrationDate = now()->toDateString();
        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $registrationNumber,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => $registrationDate,
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        try {
            $saveResponse = $this->actingAs($admin)
                ->postJson(route('admin.pendaftaran.data.update', $registrationNumber), [
                    'nama_lengkap' => 'Nama Sesudah Edit',
                    'nisn' => '1234567890',
                    'no_whatsapp' => '+628111222333',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '2010-02-03',
                    'jenis_kelamin' => 'Perempuan',
                    'agama' => 'Islam',
                    'asal_sekolah' => 'SMP Baru',
                    'alamat_domisili' => 'Alamat Baru',
                    'nama_bapak' => 'Ayah Baru',
                    'pekerjaan_bapak' => 'Guru',
                    'nama_ibu' => 'Ibu Baru',
                    'pekerjaan_ibu' => 'Dokter',
                    'no_wa_ayah' => '+628222333444',
                    'no_wa_ibu' => '+628222333445',
                    'alamat_ortu' => 'Alamat Ortu Baru',
                    'id_jurusan' => 2,
                    'id_jalur' => 2,
                    'id_gelombang' => 2,
                    'status_berkas' => 'Perbaikan Berkas',
                    'tgl_daftar' => '2026-04-20',
                    'catatan_verifikator' => 'Mohon unggah ulang KK.',
                ])
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('applicant.calon_siswa.nama_lengkap', 'Nama Sesudah Edit')
                ->assertJsonPath('applicant.calon_siswa.no_wa_ayah', '+628222333444')
                ->assertJsonPath('applicant.calon_siswa.no_wa_ibu', '+628222333445');

            $undoToken = $saveResponse->json('undo_token');
            $this->assertNotEmpty($undoToken);
            $this->assertDatabaseHas('users', [
                'id_user' => $student->id_user,
                'email' => '1234567890@example.com',
            ]);
            $this->assertDatabaseHas('calon_siswa', [
                'id_siswa' => $calonSiswa->id_siswa,
                'nama_lengkap' => 'Nama Sesudah Edit',
                'nisn' => '1234567890',
                'no_whatsapp' => '+628111222333',
                'no_wa_ayah' => '+628222333444',
                'no_wa_ibu' => '+628222333445',
            ]);
            $this->assertDatabaseHas('pendaftaran', [
                'no_registrasi' => $registrationNumber,
                'id_jurusan' => 2,
                'id_jalur' => 2,
                'id_gelombang' => 2,
                'status_berkas' => 'Perbaikan Berkas',
                'tgl_daftar' => '2026-04-20',
            ]);
            $this->assertDatabaseHas('verifikasi_panitia', [
                'no_registrasi' => $registrationNumber,
                'catatan_verifikator' => 'Mohon unggah ulang KK.',
            ]);

            $undoResponse = $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $undoToken])
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['reverse_token']);

            $this->assertDatabaseHas('users', [
                'id_user' => $student->id_user,
                'email' => $studentEmail,
            ]);
            $this->assertDatabaseHas('calon_siswa', [
                'id_siswa' => $calonSiswa->id_siswa,
                'nama_lengkap' => 'Nama Sebelum Edit',
                'nisn' => null,
                'no_whatsapp' => '081234567890',
                'alamat_domisili' => 'Alamat Lama',
            ]);
            $this->assertDatabaseHas('pendaftaran', [
                'no_registrasi' => $registrationNumber,
                'id_jurusan' => 1,
                'id_jalur' => 1,
                'id_gelombang' => 1,
                'status_berkas' => 'Menunggu Verifikasi',
                'tgl_daftar' => $registrationDate,
            ]);
            $this->assertDatabaseMissing('verifikasi_panitia', ['no_registrasi' => $registrationNumber]);

            $redoResponse = $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $undoResponse->json('reverse_token')])
                ->assertOk()
                ->assertJsonPath('success', true);
            $this->assertDatabaseHas('calon_siswa', [
                'id_siswa' => $calonSiswa->id_siswa,
                'nama_lengkap' => 'Nama Sesudah Edit',
            ]);

            $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $redoResponse->json('reverse_token')])
                ->assertOk();

            $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $undoToken])
                ->assertStatus(409);
        } finally {
            VerifikasiPanitia::where('no_registrasi', $registrationNumber)->delete();
            Panitia::where('id_user', $admin->id_user)->delete();
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
            $admin->delete();
        }
    }

    public function test_admin_drawer_supports_editing_all_student_data_including_registration_date_and_email_id(): void
    {
        $admin = User::create([

            'email' => 'admin_drawer_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $student = User::create([
            'email' => 'stu_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Nama Awal',
            'no_whatsapp' => '081234567890',
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '2009-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'asal_sekolah' => 'SMP 1 Denpasar',
            'alamat_domisili' => 'Jl. Veteran No. 1',
        ]);
        $lastReg = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($n) => ctype_digit((string) $n))
            ->map(fn ($n) => (int) $n)
            ->max() ?? 0;
        $noReg = str_pad((string) ($lastReg + 1), 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = str_pad((string) ((int) $noReg + 1), 3, '0', STR_PAD_LEFT);
        }
        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => '2026-01-10',
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        try {
            $dashboardRes = $this->actingAs($admin)->get(route('admin.dashboard'));
            $dashboardRes->assertOk();
            $dashboardRes->assertSee('edit-section-identitas', false);
            $dashboardRes->assertSee('edit-section-ortu', false);
            $dashboardRes->assertSee('edit-section-pilihan', false);
            $dashboardRes->assertSee('edit-student-tgl-daftar', false);
            $dashboardRes->assertSee('data-cell="tgl_daftar"', false);
            $dashboardRes->assertSee('data-cell="nama"', false);
            $dashboardRes->assertSee('data-cell="nisn"', false);
            $dashboardRes->assertSee('data-cell="no_whatsapp"', false);
            $dashboardRes->assertSee('select-all-applicants', false);
            $dashboardRes->assertSee('data-applicant-select', false);
            $dashboardRes->assertDontSee('selected-applicant-count', false);
            $dashboardRes->assertSee('bulk-delete-form', false);
            $dashboardRes->assertDontSee('bulk-delete-button', false);
            $dashboardRes->assertSee('bulkDeleteForm.requestSubmit()', false);
            $dashboardRes->assertDontSee('Hapus Dipilih', false);
            $dashboardRes->assertSee('confirmBulkDelete(event)', false);
            $dashboardRes->assertSee('onchange="submitAdminFilter(this)"', false);
            $dashboardRes->assertSee('downloadButton.download =', false);
            $dashboardRes->assertSee('No. WhatsApp Ayah', false);
            $dashboardRes->assertSee('No. WhatsApp Ibu', false);
            $dashboardRes->assertSee('edit-student-no-wa-ayah', false);
            $dashboardRes->assertSee('edit-student-no-wa-ibu', false);
            $dashboardRes->assertSee('applicant-row-'.$noReg, false);

            $updateRes = $this->actingAs($admin)
                ->postJson(route('admin.pendaftaran.data.update', $noReg), [
                    'nama_lengkap' => 'Nama Diubah Realtime',
                    'nisn' => '5566778899',
                    'no_whatsapp' => '+628998877665',
                    'tempat_lahir' => 'Kuta',
                    'tanggal_lahir' => '2009-08-17',
                    'jenis_kelamin' => 'Perempuan',
                    'agama' => 'Islam',
                    'asal_sekolah' => 'SMP 2 Kuta',
                    'alamat_domisili' => 'Jl. Sunset Road',
                    'nama_bapak' => null,
                    'pekerjaan_bapak' => null,
                    'nama_ibu' => 'Ibu Kandung',
                    'pekerjaan_ibu' => 'Wiraswasta',
                    'no_wa_ayah' => null,
                    'no_wa_ibu' => null,
                    'alamat_ortu' => null,
                    'id_jurusan' => 2,
                    'id_jalur' => 2,
                    'id_gelombang' => 2,
                    'status_berkas' => 'Perbaikan Berkas',
                    'tgl_daftar' => '2026-04-20',
                    'catatan_verifikator' => 'Harap upload rapor semester 1-5.',
                ]);

            $updateRes->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('applicant.no_registrasi', $noReg)
                ->assertJsonPath('applicant.calon_siswa.nama_lengkap', 'Nama Diubah Realtime');

            $this->assertDatabaseHas('pendaftaran', [
                'no_registrasi' => $noReg,
                'id_jurusan' => 2,
                'status_berkas' => 'Perbaikan Berkas',
                'tgl_daftar' => '2026-04-20',
            ]);
            $this->assertDatabaseHas('calon_siswa', [
                'id_siswa' => $calonSiswa->id_siswa,
                'nama_lengkap' => 'Nama Diubah Realtime',
                'tempat_lahir' => 'Kuta',
                'agama' => 'Islam',
            ]);
            $this->assertDatabaseHas('users', [
                'id_user' => $student->id_user,
                'email' => '5566778899@smktibaliglobalbadung.sch.id',
            ]);
            $this->assertDatabaseHas('verifikasi_panitia', [
                'no_registrasi' => $noReg,
                'catatan_verifikator' => 'Harap upload rapor semester 1-5.',
                'status_akhir' => 'Perbaikan Berkas',
            ]);

            $undoToken = $updateRes->json('undo_token');
            $this->actingAs($admin)
                ->postJson(route('admin.undo'), ['undo_token' => $undoToken])
                ->assertOk()
                ->assertJsonPath('success', true);

            $this->assertDatabaseHas('pendaftaran', [
                'no_registrasi' => $noReg,
                'tgl_daftar' => '2026-01-10',
                'status_berkas' => 'Menunggu Verifikasi',
            ]);
        } finally {
            VerifikasiPanitia::where('no_registrasi', $noReg)->delete();
            Panitia::where('id_user', $admin->id_user)->delete();
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
            $admin->delete();
        }
    }

    public function test_admin_can_filter_by_path_wave_and_registration_number_order(): void
    {
        $admin = User::create([
            'email' => 'admin_filters_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $studentRecords = [];
        $calonSiswaRecords = [];
        $registrationNumbers = [];

        $nextNumber = (Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0) + 1;

        for ($index = 0; $index < 2; $index++) {
            $studentRecords[] = User::create([
                'email' => 'student_filter_'.$index.'_'.uniqid().'@example.com',
                'password' => bcrypt('password123'),
                'role' => 'siswa',
            ]);
            $calonSiswaRecords[] = CalonSiswa::create([
                'id_user' => $studentRecords[$index]->id_user,
                'nama_lengkap' => 'Siswa Filter '.$index,
                'no_whatsapp' => '08123456789'.$index,
            ]);

            $registrationNumber = str_pad((string) $nextNumber++, 3, '0', STR_PAD_LEFT);
            while (Pendaftaran::where('no_registrasi', $registrationNumber)->exists()) {
                $registrationNumber = str_pad((string) $nextNumber++, 3, '0', STR_PAD_LEFT);
            }
            $registrationNumbers[] = $registrationNumber;

            Pendaftaran::create([
                'no_registrasi' => $registrationNumber,
                'id_siswa' => $calonSiswaRecords[$index]->id_siswa,
                'id_jurusan' => 1,
                'id_jalur' => $index + 1,
                'id_gelombang' => $index + 1,
                'tgl_daftar' => '2026-01-0'.($index + 1),
                'status_berkas' => 'Menunggu Verifikasi',
            ]);
        }

        try {
            $this->actingAs($admin)
                ->get(route('admin.dashboard', ['jalur' => 2, 'gelombang' => 2]))
                ->assertOk()
                ->assertSee('applicant-row-'.$registrationNumbers[1], false)
                ->assertDontSee('applicant-row-'.$registrationNumbers[0], false);

            $oldestResponse = $this->actingAs($admin)
                ->get(route('admin.dashboard', ['urutan_registrasi' => 'terlama']))
                ->assertOk();
            $oldestFirstPosition = strpos($oldestResponse->getContent(), 'applicant-row-'.$registrationNumbers[0]);
            $oldestSecondPosition = strpos($oldestResponse->getContent(), 'applicant-row-'.$registrationNumbers[1]);
            $this->assertNotFalse($oldestFirstPosition);
            $this->assertNotFalse($oldestSecondPosition);
            $this->assertLessThan($oldestSecondPosition, $oldestFirstPosition);

            $newestResponse = $this->actingAs($admin)
                ->get(route('admin.dashboard', ['urutan_registrasi' => 'terbaru']))
                ->assertOk();
            $newestFirstPosition = strpos($newestResponse->getContent(), 'applicant-row-'.$registrationNumbers[0]);
            $newestSecondPosition = strpos($newestResponse->getContent(), 'applicant-row-'.$registrationNumbers[1]);
            $this->assertNotFalse($newestFirstPosition);
            $this->assertNotFalse($newestSecondPosition);
            $this->assertLessThan($newestFirstPosition, $newestSecondPosition);

            // Default order without query param should default to newest registration number
            $defaultResponse = $this->actingAs($admin)
                ->get(route('admin.dashboard'))
                ->assertOk();
            $defaultFirstPosition = strpos($defaultResponse->getContent(), 'applicant-row-'.$registrationNumbers[0]);
            $defaultSecondPosition = strpos($defaultResponse->getContent(), 'applicant-row-'.$registrationNumbers[1]);
            $this->assertNotFalse($defaultFirstPosition);
            $this->assertNotFalse($defaultSecondPosition);
            $this->assertLessThan($defaultFirstPosition, $defaultSecondPosition);
            $defaultResponse->assertSee('No. Registrasi</option>', false);
            $defaultResponse->assertSee('Terbaru</option>', false);
            $defaultResponse->assertSee('Terlama</option>', false);
        } finally {
            foreach ($registrationNumbers as $registrationNumber) {
                Pendaftaran::whereKey($registrationNumber)->delete();
            }
            foreach ($calonSiswaRecords as $calonSiswa) {
                $calonSiswa->delete();
            }
            foreach ($studentRecords as $student) {
                $student->delete();
            }
            $admin->delete();
        }
    }

    public function test_admin_can_change_password_with_matching_confirmation(): void
    {
        $admin = User::create([
            'email' => 'admin_password_'.time().'@example.com',
            'password' => 'old-password-123',
            'role' => 'admin',
        ]);

        try {
            $this->actingAs($admin)
                ->get(route('admin.password.edit'))
                ->assertStatus(200)
                ->assertSee('Ganti Password Admin')
                ->assertSee('KONFIRMASI PASSWORD');

            $response = $this->actingAs($admin)->put(route('admin.password.update'), [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

            $response->assertRedirect(route('admin.password.edit'));
            $response->assertSessionHas('success');
            $this->assertTrue(Hash::check('new-password-123', $admin->fresh()->password));
        } finally {
            $admin->delete();
        }
    }

    public function test_admin_password_change_rejects_mismatched_confirmation(): void
    {
        $admin = User::create([
            'email' => 'admin_password_mismatch_'.time().'@example.com',
            'password' => 'old-password-123',
            'role' => 'admin',
        ]);

        try {
            $response = $this->actingAs($admin)->put(route('admin.password.update'), [
                'password' => 'new-password-123',
                'password_confirmation' => 'different-password',
            ]);

            $response->assertSessionHasErrors('password');
            $this->assertTrue(Hash::check('old-password-123', $admin->fresh()->password));
        } finally {
            $admin->delete();
        }
    }

    public function test_student_registration_and_login_flow(): void
    {
        $testEmail = 'calon_siswa_test_'.time().'@example.com';

        $response = $this->post(route('register.post'), [
            'nama_lengkap' => 'Calon Siswa Pengujian',
            'email' => $testEmail,
            'no_whatsapp' => '081299887766',
            'asal_sekolah' => 'SMP Negeri 1 Denpasar',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('pendaftaran.step1'));
        $this->assertAuthenticated();

        $user = User::where('email', $testEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('siswa', $user->role);
        $this->assertNotNull($user->calonSiswa);
        $this->assertEquals('Calon Siswa Pengujian', $user->calonSiswa->nama_lengkap);

        // Akses step 1
        $step1Res = $this->actingAs($user)->get(route('pendaftaran.step1'));
        $step1Res->assertStatus(200);
        $step1Res->assertSee('Calon Siswa Pengujian');
        $step1Res->assertSee('id="input-nisn"', false);
        $step1Res->assertSee('id="error-nisn"', false);
        $step1Res->assertSee('NISN harus terdiri dari 10 digit angka.');
        $step1Res->assertDontSee(route('pendaftaran.step2'));
        $step1Res->assertDontSee(route('pendaftaran.step3'));
        $step1Res->assertDontSee(route('pendaftaran.step4'));

        $step2Res = $this->actingAs($user)->get(route('pendaftaran.step2'));
        $step2Res->assertStatus(200);
        $step2Res->assertSee('TKC (Tata Kecantikan)');
        $step2Res->assertSee('id="nama-ayah"', false);
        $step2Res->assertSee('id="pekerjaan-ayah"', false);
        $step2Res->assertSee('id="nama-ibu"', false);
        $step2Res->assertSee('id="pekerjaan-ibu"', false);
        $step2Res->assertSee('name="no_wa_ayah"', false);
        $step2Res->assertSee('name="no_wa_ibu"', false);
        $step2Res->assertSee('Nama tidak boleh mengandung angka atau simbol khusus.');
        // Di step 2: Hanya tombol step 1 yang dapat diakses (step 3 dan 4 dinonaktifkan)
        $step2Res->assertSee(route('pendaftaran.step1'));
        $step2Res->assertDontSee(route('pendaftaran.step3'));
        $step2Res->assertDontSee(route('pendaftaran.step4'));

        // Test validasi nama ayah/ibu tidak boleh mengandung angka atau karakter terlarang
        $invalidStep2Res = $this->actingAs($user)->post(route('pendaftaran.step2.post'), [
            'nama_bapak' => 'Bapak123!',
            'pekerjaan_bapak' => 'Wiraswasta',
            'nama_ibu' => 'Ibu Pengujian',
            'pekerjaan_ibu' => 'PNS / ASN',
            'no_wa_ayah' => '+6281234567890',
            'no_wa_ibu' => '+6281234567891',
            'alamat_ortu' => 'Jl. Pengujian No. 123',
            'id_jurusan' => 1,
            'id_jalur' => 1,
        ]);
        $invalidStep2Res->assertSessionHasErrors(['nama_bapak']);

        // Step 2: Kirim data ortu dan jurusan tanpa input gelombang (otomatis dari waktu/sistem)
        $step2PostRes = $this->actingAs($user)->post(route('pendaftaran.step2.post'), [
            'nama_bapak' => 'Bapak Pengujian, S.T.',
            'pekerjaan_bapak' => 'Wiraswasta',
            'nama_ibu' => 'Ibu Pengujian, S.Pd.',
            'pekerjaan_ibu' => 'PNS / ASN',
            'no_wa_ayah' => '+6281234567890',
            'no_wa_ibu' => '+6281234567891',
            'alamat_ortu' => 'Jl. Pengujian No. 123',
            'id_jurusan' => 1,
            'id_jalur' => 1,
        ]);

        $step2PostRes->assertRedirect(route('pendaftaran.step3'));

        // Pastikan gelombang pendaftaran otomatis terisi di session draft
        $draft = session('pendaftaran_draft');
        $this->assertNotNull($draft);
        $this->assertArrayHasKey('id_gelombang', $draft);
        $this->assertNotEmpty($draft['id_gelombang']);
        $this->assertDatabaseHas('calon_siswa', [
            'id_user' => $user->id_user,
            'no_wa_ayah' => '+6281234567890',
            'no_wa_ibu' => '+6281234567891',
        ]);

        // Bersihkan data tes
        $user->delete();
    }

    public function test_admin_can_change_active_wave_and_it_reflects_on_landing_page(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_wave@example.com'],
            ['password' => bcrypt('admin123'), 'role' => 'admin']
        );

        // 1. Ubah ke Gelombang 1
        $res = $this->actingAs($admin)->postJson(route('admin.setWave'), [
            'wave' => 'Gelombang 1',
        ]);
        $res->assertStatus(200)->assertJson(['success' => true, 'wave' => 'Gelombang 1']);

        // Cek halaman utama menampilkan Gelombang 1 aktif
        Auth::logout();
        $homeRes = $this->get(route('home'));
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Gelombang 1 sedang dibuka');
        $homeRes->assertSee('Sedang Berlangsung');

        // 2. Ubah ke Tutup Gelombang
        $resClosed = $this->actingAs($admin)->postJson(route('admin.setWave'), [
            'wave' => 'Tutup Gelombang',
        ]);
        $resClosed->assertStatus(200)->assertJson(['success' => true, 'wave' => 'Tutup Gelombang']);

        $this->getJson(route('home.wave-status'))
            ->assertStatus(200)
            ->assertJson(['wave' => 'Tutup Gelombang']);

        // Cek halaman utama menampilkan status ditutup
        Auth::logout();
        $homeClosedRes = $this->get(route('home'));
        $homeClosedRes->assertStatus(200);
        $homeClosedRes->assertSee('Pendaftaran sedang ditutup');
        $homeClosedRes->assertSee('Pendaftaran Ditutup');
        $homeClosedRes->assertDontSee(route('pendaftaran.step1'), false);
        $homeClosedRes->assertDontSee(route('pendaftaran.step2'), false);
        $homeClosedRes->assertDontSee(route('register'), false);

        // 3. Kembalikan ke Gelombang Khusus
        $this->actingAs($admin)->postJson(route('admin.setWave'), [
            'wave' => 'Gelombang Khusus',
        ]);

        $admin->delete();
    }

    public function test_admin_can_update_jurusan_quota(): void
    {
        $admin = User::create([
            'email' => 'admin_quota_'.time().'@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Jurusan Tes Kuota '.uniqid(),
            'kuota' => 72,
        ]);
        $originalQuota = 72;
        $updatedQuota = $originalQuota + 1;

        try {
            $response = $this->actingAs($admin)->postJson(
                route('admin.jurusan.kuota.update', ['jurusan' => $jurusan->getKey()]),
                ['kuota' => $updatedQuota]
            );

            $response->assertOk()->assertJson([
                'success' => true,
                'kuota' => $updatedQuota,
            ]);
            $this->assertSame($updatedQuota, (int) $jurusan->fresh()->kuota);

            $this->actingAs($admin)->postJson(
                route('admin.jurusan.kuota.update', ['jurusan' => $jurusan->getKey()]),
                ['kuota' => 0]
            )->assertUnprocessable()->assertJsonValidationErrors('kuota');
        } finally {
            $jurusan->delete();
            $admin->delete();
        }
    }

    public function test_admin_cannot_accept_more_students_than_jurusan_quota(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Jurusan Tes Limit '.uniqid(),
            'kuota' => 1,
        ]);
        $admin = User::create([
            'email' => 'admin_quota_limit_'.uniqid().'@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        Panitia::create([
            'id_user' => $admin->id_user,
            'nama_panitia' => 'Panitia Tes Kuota',
            'nip' => 'TEST-'.uniqid(),
            'no_telepon' => '081234567890',
            'bidang_studi' => 'SPMB',
        ]);
        $students = [];

        try {
            foreach (['first'] as $label) {
                $student = User::create([
                    'email' => "quota_{$label}_".uniqid().'@example.com',
                    'password' => bcrypt('student123'),
                    'role' => 'siswa',
                ]);
                $students[] = $student;

                $this->actingAs($student)->withSession([
                    'pendaftaran_draft' => [
                        'id_jurusan' => $jurusan->id_jurusan,
                        'id_jalur' => 1,
                        'id_gelombang' => 1,
                    ],
                    'berkas_draft' => $this->completeDocumentDraft(),
                ])->post(route('pendaftaran.submit'))->assertRedirect(route('pendaftaran.card'));
            }

            $studentIds = CalonSiswa::whereIn('id_user', collect($students)->pluck('id_user'))
                ->pluck('id_siswa');
            $registrations = Pendaftaran::whereIn('id_siswa', $studentIds)->orderBy('no_registrasi')->get();
            $this->assertCount(1, $registrations);

            $expectedOccupancy = Pendaftaran::where('id_jurusan', $jurusan->id_jurusan)->count();

            $assertJurusanOccupancy = function () use ($admin, $jurusan, $expectedOccupancy): void {
                $acceptedCount = Pendaftaran::where('id_jurusan', $jurusan->id_jurusan)
                    ->where('status_berkas', 'Diterima')
                    ->count();
                $expectedAcceptedPercentage = min(100, (int) round(($acceptedCount / (int) $jurusan->kuota) * 100));

                $response = $this->actingAs($admin)
                    ->get(route('admin.dashboard'))
                    ->assertOk();
                $jurusanStat = $response->viewData('jurusanStats')->firstWhere('id_jurusan', $jurusan->getKey());

                $this->assertSame($expectedOccupancy, $jurusanStat['terisi']);
                $this->assertSame(100, $jurusanStat['persen']);
                $this->assertSame($expectedAcceptedPercentage, $jurusanStat['persen_diterima']);
                $response->assertSee("{$acceptedCount} / {$jurusan->kuota}");
            };

            $assertJurusanOccupancy();

            foreach (['Perbaikan Berkas', 'Ditolak'] as $status) {
                $this->actingAs($admin)
                    ->from(route('admin.dashboard'))
                    ->post(route('admin.verifikasi', $registrations[0]->no_registrasi), [
                        'status_akhir' => $status,
                    ])
                    ->assertRedirect(route('admin.dashboard'));

                $assertJurusanOccupancy();
            }

            $this->actingAs($admin)
                ->from(route('admin.dashboard'))
                ->post(route('admin.verifikasi', $registrations[0]->no_registrasi), [
                    'status_akhir' => 'Diterima',
                ])
                ->assertRedirect(route('admin.dashboard'));

            $assertJurusanOccupancy();

            $secondStudent = User::create([
                'email' => 'quota_second_'.uniqid().'@example.com',
                'password' => bcrypt('student123'),
                'role' => 'siswa',
            ]);
            $students[] = $secondStudent;
            $this->actingAs($secondStudent)->withSession([
                'pendaftaran_draft' => [
                    'id_jurusan' => $jurusan->id_jurusan,
                    'id_jalur' => 1,
                    'id_gelombang' => 1,
                ],
                'berkas_draft' => $this->completeDocumentDraft(),
            ])->post(route('pendaftaran.submit'))->assertRedirect(route('pendaftaran.card'));
            $secondStudentProfile = CalonSiswa::where('id_user', $secondStudent->id_user)->firstOrFail();
            $registrations->push(Pendaftaran::where('id_siswa', $secondStudentProfile->id_siswa)->firstOrFail());

            $this->actingAs($admin)
                ->get(route('admin.dashboard'))
                ->assertSee('Kuota penerimaan penuh (1/1)');

            $this->actingAs($admin)
                ->from(route('admin.dashboard'))
                ->post(route('admin.verifikasi', $registrations[1]->no_registrasi), [
                    'status_akhir' => 'Diterima',
                ])
                ->assertRedirect(route('admin.dashboard'))
                ->assertSessionHas('quota_warning');

            $this->assertSame('Menunggu Verifikasi', $registrations[1]->fresh()->status_berkas);

            $this->actingAs($admin)->postJson(
                route('admin.jurusan.kuota.update', ['jurusan' => $jurusan->getKey()]),
                ['kuota' => 2]
            )->assertOk();

            $this->actingAs($admin)
                ->get(route('admin.dashboard'))
                ->assertDontSee('Kuota penerimaan penuh (1/1)');

            $this->actingAs($admin)
                ->from(route('admin.dashboard'))
                ->post(route('admin.verifikasi', $registrations[1]->no_registrasi), [
                    'status_akhir' => 'Diterima',
                ])
                ->assertRedirect(route('admin.dashboard'));

            $this->assertSame('Diterima', $registrations[1]->fresh()->status_berkas);
        } finally {
            $studentIds = CalonSiswa::whereIn('id_user', collect($students)->pluck('id_user'))
                ->pluck('id_siswa');
            $studentRegistrations = Pendaftaran::whereIn('id_siswa', $studentIds)->get();
            foreach ($studentRegistrations as $registration) {
                $registration->verifikasi()->delete();
            }
            Pendaftaran::whereIn('id_siswa', $studentIds)->delete();
            CalonSiswa::whereIn('id_user', collect($students)->pluck('id_user'))->delete();
            foreach ($students as $student) {
                $student->delete();
            }
            $jurusan->delete();
            $admin->panitia?->delete();
            $admin->delete();
        }
    }

    public function test_step3_photo_upload_and_storage_fallback_route(): void
    {
        Storage::fake('public');

        $student = User::create([
            'email' => 'photo_test_'.uniqid().'@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Uji Foto',
            'no_whatsapp' => '081299998888',
        ]);

        try {
            $viewRes = $this->actingAs($student)->get(route('pendaftaran.step3'));
            $viewRes->assertOk();
            $viewRes->assertSee('card-upload-pasfoto', false);
            $viewRes->assertDontSee('preview-box-pasfoto', false);
            // Di step 3: Hanya langkah 1 dan 2 yang aktif, langkah 4 tidak dapat diakses
            $viewRes->assertSee(route('pendaftaran.step1'));
            $viewRes->assertSee(route('pendaftaran.step2'));
            $viewRes->assertDontSee(route('pendaftaran.step4'));
            $viewContent = $viewRes->getContent();
            $this->assertMatchesRegularExpression('/name="berkas_foto"[^>]*\brequired\b/', $viewContent);
            $this->assertMatchesRegularExpression('/name="berkas_kk"[^>]*\brequired\b/', $viewContent);
            $this->assertMatchesRegularExpression('/name="berkas_akta"[^>]*\brequired\b/', $viewContent);

            $fakePhoto = UploadedFile::fake()->create('pasfoto_formal.jpg', 300, 'image/jpeg');

            $incompleteUploadRes = $this->actingAs($student)->post(route('pendaftaran.step3.post'), [
                'berkas_foto' => $fakePhoto,
            ]);

            $incompleteUploadRes->assertSessionHasErrors(['berkas_kk', 'berkas_akta']);

            $this->actingAs($student)->withSession([
                'pendaftaran_draft' => [
                    'id_jurusan' => 1,
                    'id_jalur' => 1,
                    'id_gelombang' => 1,
                ],
                'berkas_draft' => [
                    'berkas_foto' => [
                        'jenis_berkas' => 'Pasfoto Formal',
                        'nama_file' => 'pasfoto_formal.jpg',
                        'ukuran_file' => 300,
                        'path_file' => 'storage/berkas/pasfoto_formal.jpg',
                    ],
                ],
            ])->post(route('pendaftaran.submit'))->assertRedirect(route('pendaftaran.step3'));

            $uploadRes = $this->actingAs($student)->post(route('pendaftaran.step3.post'), [
                'berkas_foto' => UploadedFile::fake()->create('pasfoto_formal.jpg', 300, 'image/jpeg'),
                'berkas_kk' => UploadedFile::fake()->create('kartu_keluarga.pdf', 300, 'application/pdf'),
                'berkas_akta' => UploadedFile::fake()->create('akta_kelahiran.pdf', 300, 'application/pdf'),
            ]);

            $uploadRes->assertRedirect(route('pendaftaran.step4'));
            $uploadRes->assertSessionHas('success');

            $draft = session('berkas_draft');
            $this->assertIsArray($draft);
            $this->assertArrayHasKey('berkas_foto', $draft);
            $this->assertArrayHasKey('berkas_kk', $draft);
            $this->assertArrayHasKey('berkas_akta', $draft);
            $this->assertSame('Pasfoto Formal', $draft['berkas_foto']['jenis_berkas']);
            $this->assertStringStartsWith('storage/berkas/', $draft['berkas_foto']['path_file']);

            // Test fallback route /storage/berkas/{filename}
            $filename = basename($draft['berkas_foto']['path_file']);
            // Put fake file in physical storage test path
            $realPath = storage_path('app/public/berkas/'.$filename);
            if (! is_dir(dirname($realPath))) {
                mkdir(dirname($realPath), 0755, true);
            }
            file_put_contents($realPath, 'fake-image-content');

            try {
                $storageRes = $this->get('/storage/berkas/'.$filename);
                $storageRes->assertOk();

                // Test storage route fallback for /storage/{path}
                $storageFileRes = $this->get('/storage/berkas/'.$filename);
                $storageFileRes->assertOk();
            } finally {
                if (file_exists($realPath)) {
                    @unlink($realPath);
                }
            }

            // Test step 3 view with existing draft shows preview image and fallback url
            $viewWithDraft = $this->actingAs($student)->withSession(['berkas_draft' => $draft])->get(route('pendaftaran.step3'));
            $viewWithDraft->assertOk();
            $viewWithDraft->assertSee('data-fallback-url', false);
            $viewWithDraft->assertSee($filename, false);

            $viewWithPartialDraft = $this->actingAs($student)->withSession([
                'berkas_draft' => ['berkas_foto' => $draft['berkas_foto']],
            ])->get(route('pendaftaran.step3'));
            $partialDraftContent = $viewWithPartialDraft->getContent();
            $this->assertDoesNotMatchRegularExpression('/name="berkas_foto"[^>]*\brequired\b/', $partialDraftContent);
            $this->assertMatchesRegularExpression('/name="berkas_kk"[^>]*\brequired\b/', $partialDraftContent);
            $this->assertMatchesRegularExpression('/name="berkas_akta"[^>]*\brequired\b/', $partialDraftContent);
        } finally {
            $calonSiswa->delete();
            $student->delete();
        }
    }

    public function test_participant_card_displays_school_logo_and_photo(): void
    {
        $student = User::create([
            'email' => 'student_card_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Kartu Tes',
            'no_whatsapp' => '081234567899',
            'alamat_domisili' => 'Denpasar',
            'alamat_ortu' => 'Denpasar',
        ]);

        $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $registrationNumber = str_pad((string) ($lastRegistrationNumber + 1), 3, '0', STR_PAD_LEFT);

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $registrationNumber,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Diterima',
        ]);

        $testPhotoName = 'test_foto_card_'.uniqid().'.png';
        $testPhotoDiskPath = storage_path('app/public/berkas/'.$testPhotoName);
        if (! is_dir(dirname($testPhotoDiskPath))) {
            mkdir(dirname($testPhotoDiskPath), 0755, true);
        }
        file_put_contents($testPhotoDiskPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

        $berkas = BerkasPersyaratan::create([
            'no_registrasi' => $pendaftaran->no_registrasi,
            'jenis_berkas' => 'Pasfoto Formal',
            'nama_file' => $testPhotoName,
            'ukuran_file' => 100,
            'path_file' => 'storage/berkas/'.$testPhotoName,
        ]);

        $adminUser = User::where('role', 'admin')->first();

        try {
            // Check student participant card
            $cardResponse = $this->actingAs($student)->get(route('pendaftaran.card'));
            $cardResponse->assertOk();
            $cardResponse->assertSee('Logo SMK TI Bali Global Badung');
            $cardResponse->assertSee('Pasfoto Siswa Kartu Tes');
            $cardResponse->assertSee('data:image/png;base64', false);

            // Check admin card view
            if ($adminUser) {
                $adminCardResponse = $this->actingAs($adminUser)->get(route('admin.kartu', ['no_registrasi' => $registrationNumber]));
                $adminCardResponse->assertOk();
                $adminCardResponse->assertSee('Logo SMK TI Bali Global Badung');
                $adminCardResponse->assertSee('Pasfoto Siswa Kartu Tes');
                $adminCardResponse->assertSee('data:image/png;base64', false);

                // Check admin dashboard has logo in printable-id-card
                $adminDashboard = $this->actingAs($adminUser)->get(route('admin.dashboard'));
                $adminDashboard->assertOk();
                $adminDashboard->assertSee('Logo SMK TI Bali Global Badung');
            }
        } finally {
            if (file_exists($testPhotoDiskPath)) {
                @unlink($testPhotoDiskPath);
            }
            $berkas->delete();
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
        }
    }

    public function test_wave_closure_blocks_student_registration_steps_and_submission(): void
    {
        Cache::forever('ppdb_active_wave', 'Tutup Gelombang');

        $student = User::create([
            'email' => 'wave_closed_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Tes Gelombang Tutup',
            'no_whatsapp' => '081234567890',
        ]);

        try {
            $this->actingAs($student)->get(route('pendaftaran.step1'))
                ->assertRedirect(route('home'))
                ->assertSessionHas('error');

            $this->actingAs($student)->get(route('pendaftaran.step2'))
                ->assertRedirect(route('home'))
                ->assertSessionHas('error');

            $this->actingAs($student)->get(route('pendaftaran.step3'))
                ->assertRedirect(route('home'))
                ->assertSessionHas('error');

            $this->actingAs($student)->get(route('pendaftaran.step4'))
                ->assertRedirect(route('home'))
                ->assertSessionHas('error');

            $this->actingAs($student)->post(route('pendaftaran.submit'))
                ->assertRedirect(route('home'))
                ->assertSessionHas('error');
        } finally {
            Cache::forever('ppdb_active_wave', 'Gelombang Khusus');
            $calonSiswa->delete();
            $student->delete();
        }
    }

    public function test_student_with_existing_registration_cannot_access_steps_or_double_submit(): void
    {
        $student = User::create([
            'email' => 'duplicate_guard_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Anti Dobel',
            'no_whatsapp' => '081234567890',
        ]);

        $lastNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($n) => ctype_digit((string) $n))
            ->map(fn ($n) => (int) $n)
            ->max() ?? 0;
        $noReg = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = str_pad((string) ((int) $noReg + 1), 3, '0', STR_PAD_LEFT);
        }

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        try {
            $this->actingAs($student)->get(route('pendaftaran.step1'))
                ->assertRedirect(route('pendaftaran.card'));

            $this->actingAs($student)->get(route('pendaftaran.step2'))
                ->assertRedirect(route('pendaftaran.card'));

            $this->actingAs($student)->get(route('pendaftaran.step3'))
                ->assertRedirect(route('pendaftaran.card'));

            $this->actingAs($student)->get(route('pendaftaran.step4'))
                ->assertRedirect(route('pendaftaran.card'));

            $this->actingAs($student)->post(route('pendaftaran.submit'))
                ->assertRedirect(route('pendaftaran.card'))
                ->assertSessionHas('info');
        } finally {
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
        }
    }

    public function test_student_can_reupload_documents_when_status_is_perbaikan_berkas(): void
    {
        Storage::fake('public');

        $student = User::create([
            'email' => 'revisi_test_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Tes Revisi',
            'no_whatsapp' => '081234567890',
        ]);

        $lastNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($n) => ctype_digit((string) $n))
            ->map(fn ($n) => (int) $n)
            ->max() ?? 0;
        $noReg = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = str_pad((string) ((int) $noReg + 1), 3, '0', STR_PAD_LEFT);
        }

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Perbaikan Berkas',
        ]);

        $initialBerkas = BerkasPersyaratan::create([
            'no_registrasi' => $noReg,
            'jenis_berkas' => 'Kartu Keluarga (KK)',
            'nama_file' => 'kk_buram.jpg',
            'ukuran_file' => 50000,
            'path_file' => 'storage/berkas/kk_buram.jpg',
        ]);

        try {
            $cardRes = $this->actingAs($student)->get(route('pendaftaran.card'));
            $cardRes->assertOk();
            $cardRes->assertSee('form-revisi-berkas', false);
            $cardRes->assertSee('Formulir Unggah Ulang Berkas');

            $newKkFile = UploadedFile::fake()->create('kk_jelas.pdf', 800, 'application/pdf');

            $uploadRes = $this->actingAs($student)->post(route('pendaftaran.revisi-berkas'), [
                'berkas_kk' => $newKkFile,
            ]);

            $uploadRes->assertRedirect(route('pendaftaran.card'));
            $uploadRes->assertSessionHas('success');

            $pendaftaran->refresh();
            $this->assertSame('Menunggu Verifikasi', $pendaftaran->status_berkas);

            $updatedBerkas = BerkasPersyaratan::where('no_registrasi', $noReg)
                ->where('jenis_berkas', 'Kartu Keluarga (KK)')
                ->first();
            $this->assertNotNull($updatedBerkas);
            $this->assertSame('kk_jelas.pdf', $updatedBerkas->nama_file);
        } finally {
            BerkasPersyaratan::where('no_registrasi', $noReg)->delete();
            $pendaftaran->delete();
            $calonSiswa->delete();
            $student->delete();
        }
    }

    /**
     * @return array<string, array<string, int|string>>
     */
    public function test_step1_rejects_out_of_range_or_future_birth_date(): void
    {
        $user = User::create([
            'email' => 'test_birth_date_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Siswa Pengujian',
            'no_whatsapp' => '081234567890',
        ]);

        $testNisn = '77'.random_int(10000000, 99999999);
        $response = $this->actingAs($user)->post(route('pendaftaran.step1.post'), [
            'nama_lengkap' => 'Siswa Pengujian',
            'nisn' => $testNisn,
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '21212-12-11',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'asal_sekolah' => 'SMP Negeri 1',
            'alamat_domisili' => 'Denpasar',
            'no_whatsapp' => '081234567890',
        ]);

        $response->assertSessionHasErrors('tanggal_lahir');
        $this->assertDatabaseMissing('calon_siswa', ['nisn' => $testNisn]);
    }

    public function test_keuangan_delete_student_with_finances_cleans_up_successfully(): void
    {
        $student = User::create([
            'email' => 'keu_del_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);
        $calonSiswa = CalonSiswa::create([
            'id_user' => $student->id_user,
            'nama_lengkap' => 'Siswa Keuangan Test',
            'nisn' => '99'.random_int(10000000, 99999999),
            'no_whatsapp' => '081234567890',
        ]);

        $lastNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $noReg = (string) ($lastNumber + 1);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = (string) ((int) $noReg + 1);
        }

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now()->toDateString(),
            'status_berkas' => 'Menunggu Verifikasi',
        ]);

        BerkasPersyaratan::create([
            'no_registrasi' => $pendaftaran->no_registrasi,
            'jenis_berkas' => 'Pasfoto Formal',
            'nama_file' => 'test.jpg',
            'path_file' => 'storage/berkas/test.jpg',
            'ukuran_file' => 100,
        ]);

        $panitia = Panitia::first();
        VerifikasiPanitia::create([
            'no_registrasi' => $pendaftaran->no_registrasi,
            'id_panitia' => $panitia?->id_panitia,
            'tgl_verifikasi' => now()->toDateString(),
            'status_akhir' => 'Menunggu Verifikasi',
        ]);

        $keuanganService = app(KeuanganService::class);
        $keuanganService->deleteStudentWithFinances($pendaftaran, 'Admin Uji QA');

        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran->no_registrasi]);
        $this->assertDatabaseMissing('berkas_persyaratan', ['no_registrasi' => $pendaftaran->no_registrasi]);
        $this->assertDatabaseMissing('verifikasi_panitia', ['no_registrasi' => $pendaftaran->no_registrasi]);
        $this->assertDatabaseMissing('calon_siswa', ['id_siswa' => $calonSiswa->id_siswa]);
        $this->assertDatabaseMissing('users', ['id_user' => $student->id_user]);
    }

    private function completeDocumentDraft(): array
    {
        return [
            'berkas_foto' => [
                'jenis_berkas' => 'Pasfoto Formal',
                'nama_file' => 'pasfoto.jpg',
                'ukuran_file' => 1,
                'path_file' => 'storage/berkas/pasfoto.jpg',
            ],
            'berkas_kk' => [
                'jenis_berkas' => 'Kartu Keluarga (KK)',
                'nama_file' => 'kartu-keluarga.pdf',
                'ukuran_file' => 1,
                'path_file' => 'storage/berkas/kartu-keluarga.pdf',
            ],
            'berkas_akta' => [
                'jenis_berkas' => 'Akta Kelahiran Asli',
                'nama_file' => 'akta-kelahiran.pdf',
                'ukuran_file' => 1,
                'path_file' => 'storage/berkas/akta-kelahiran.pdf',
            ],
        ];
    }
}
