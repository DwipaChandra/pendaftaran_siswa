<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\PembayaranSiswa;
use App\Models\PembayaranSiswaItem;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDeletionRedirectFeatureTest extends TestCase
{
    private function createAdmin(): User
    {
        return User::create([
            'email' => 'admin_test_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
    }

    private function createApplicant(): Pendaftaran
    {
        $studentUser = User::create([
            'email' => 'siswa_del_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $studentUser->id_user,
            'nama_lengkap' => 'Siswa Tes Delete',
            'nisn' => '88'.random_int(10000000, 99999999),
            'tempat_lahir' => 'Badung',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'asal_sekolah' => 'SMPN 1 Kuta',
            'alamat_domisili' => 'Kuta',
            'no_whatsapp' => '081234567888',
        ]);

        $lastRegistrationNumber = Pendaftaran::pluck('no_registrasi')
            ->filter(fn ($number) => ctype_digit((string) $number))
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;
        $noReg = (string) ($lastRegistrationNumber + 1);
        while (Pendaftaran::where('no_registrasi', $noReg)->exists()) {
            $noReg = (string) ((int) $noReg + 1);
        }

        return Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => 1,
            'id_jalur' => 1,
            'id_gelombang' => 1,
            'tgl_daftar' => now(),
            'status_berkas' => 'Menunggu Verifikasi',
        ]);
    }

    public function test_single_delete_with_halaman_redirects_to_daftar_ulang(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran = $this->createApplicant();

        $response = $this->actingAs($admin)->delete(
            route('admin.pendaftaran.destroy', ['no_registrasi' => $pendaftaran->no_registrasi]),
            [
                'halaman' => 'daftar-ulang',
                'redirect_to' => route('admin.daftar-ulang'),
            ]
        );

        $response->assertRedirect(route('admin.daftar-ulang'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran->no_registrasi]);

        $admin->delete();
    }

    public function test_single_delete_with_referer_preserves_daftar_ulang_page(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran = $this->createApplicant();

        $response = $this->actingAs($admin)
            ->from(route('admin.daftar-ulang'))
            ->delete(route('admin.pendaftaran.destroy', ['no_registrasi' => $pendaftaran->no_registrasi]));

        $response->assertRedirect(route('admin.daftar-ulang'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran->no_registrasi]);

        $admin->delete();
    }

    public function test_single_delete_default_redirects_to_tabel_jurusan_not_beranda(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran = $this->createApplicant();

        $response = $this->actingAs($admin)
            ->delete(route('admin.pendaftaran.destroy', ['no_registrasi' => $pendaftaran->no_registrasi]));

        $response->assertRedirect(route('admin.tabel-jurusan'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran->no_registrasi]);

        $admin->delete();
    }

    public function test_bulk_delete_redirects_to_tabel_jurusan(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran1 = $this->createApplicant();
        $pendaftaran2 = $this->createApplicant();

        $response = $this->actingAs($admin)->post(
            route('admin.pendaftaran.destroy-selected'),
            [
                'no_registrasi' => [$pendaftaran1->no_registrasi, $pendaftaran2->no_registrasi],
                'halaman' => 'tabel-jurusan',
                'redirect_to' => route('admin.tabel-jurusan'),
            ]
        );

        $response->assertRedirect(route('admin.tabel-jurusan'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran1->no_registrasi]);
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran2->no_registrasi]);

        $admin->delete();
    }

    public function test_bulk_delete_handles_duplicate_registration_numbers_in_request(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran1 = $this->createApplicant();
        $pendaftaran2 = $this->createApplicant();

        // Mengirimkan array duplikat seperti yang sebelumnya terjadi pada form browser
        $response = $this->actingAs($admin)->post(
            route('admin.pendaftaran.destroy-selected'),
            [
                'no_registrasi' => [
                    $pendaftaran1->no_registrasi,
                    $pendaftaran1->no_registrasi,
                    $pendaftaran2->no_registrasi,
                    $pendaftaran2->no_registrasi,
                ],
                'halaman' => 'tabel-jurusan',
                'redirect_to' => route('admin.tabel-jurusan'),
            ]
        );

        $response->assertRedirect(route('admin.tabel-jurusan'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran1->no_registrasi]);
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran2->no_registrasi]);

        $admin->delete();
    }

    public function test_bulk_delete_removes_applicant_even_with_payment_records(): void
    {
        $admin = $this->createAdmin();
        $pendaftaran = $this->createApplicant();

        // Buat pembayaran untuk siswa
        $pembayaran = PembayaranSiswa::create([
            'no_kuitansi' => 'KWT-TEST-'.uniqid(),
            'no_registrasi' => $pendaftaran->no_registrasi,
            'tipe_transaksi' => 'pembayaran',
            'total_bayar' => 150000,
            'metode_pembayaran' => 'Tunai',
            'tgl_bayar' => now(),
            'catatan' => 'Tes bayar',
        ]);

        PembayaranSiswaItem::create([
            'pembayaran_id' => $pembayaran->id,
            'kategori' => 'pendaftaran',
            'nominal' => 150000,
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.pendaftaran.destroy-selected'),
            [
                'no_registrasi' => [$pendaftaran->no_registrasi],
                'halaman' => 'tabel-jurusan',
                'redirect_to' => route('admin.tabel-jurusan'),
            ]
        );

        $response->assertRedirect(route('admin.tabel-jurusan'));
        $this->assertDatabaseMissing('pendaftaran', ['no_registrasi' => $pendaftaran->no_registrasi]);
        $this->assertDatabaseMissing('pembayaran_siswa', ['id' => $pembayaran->id]);

        $admin->delete();
    }
}
