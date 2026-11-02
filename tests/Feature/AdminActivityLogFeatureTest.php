<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\CalonSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminActivityLogFeatureTest extends TestCase
{
    public function test_admin_activity_log_is_recorded_when_updating_daftar_ulang_status(): void
    {
        $admin = User::create([
            'email' => 'admin_log_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $studentUser = User::create([
            'email' => 'student_log_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'siswa',
        ]);

        $calonSiswa = CalonSiswa::create([
            'id_user' => $studentUser->id_user,
            'nama_lengkap' => 'Budi Santoso',
            'nisn' => (string) rand(1000000000, 9999999999),
            'asal_sekolah' => 'SMP Negeri 1 Denpasar',
            'no_whatsapp' => '081234567890',
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
            'alamat_domisili' => 'Jl. Veteran No. 1, Denpasar',
        ]);

        $jurusan = Jurusan::first() ?: Jurusan::create(['nama_jurusan' => 'RPL', 'kuota' => 72]);
        $jalur = JalurPendaftaran::first() ?: JalurPendaftaran::create(['nama_jalur' => 'Reguler']);
        $gelombang = GelombangPendaftaran::first() ?: GelombangPendaftaran::create(['nama_gelombang' => 'Gelombang 1']);

        $noReg = '999';
        Cache::forget('daftar_ulang_status_'.$noReg);
        Cache::forget('daftar_ulang_date_'.$noReg);
        Cache::forget('daftar_ulang_pembayaran_'.$noReg);
        Pendaftaran::whereKey($noReg)->delete();
        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $noReg,
            'id_siswa' => $calonSiswa->id_siswa,
            'id_jurusan' => $jurusan->id_jurusan,
            'id_jalur' => $jalur->id_jalur,
            'id_gelombang' => $gelombang->id_gelombang,
            'tgl_daftar' => now(),
            'status_berkas' => 'Diterima',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.daftar-ulang.status'), [
            'no_registrasi' => $noReg,
            'status' => 'Sudah Daftar Ulang',
            'status_pembayaran_awal' => 'Nyicil',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'status' => 'Sudah Daftar Ulang',
            'status_pembayaran_awal' => 'Nyicil',
        ]);
        $this->assertMatchesRegularExpression(
            '/^\d{2} [A-Z][a-z]{2} \d{4} \d{2}:\d{2}$/',
            $response->json('tgl_daftar_ulang')
        );
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            Cache::get('daftar_ulang_date_'.$noReg)
        );
        $response->assertJsonStructure([
            'log' => [
                'id',
                'admin_name',
                'action_type',
                'title',
                'description',
                'badge_label',
                'date_formatted',
                'created_at_ts',
            ],
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'no_registrasi' => $noReg,
            'action_type' => 'daftar_ulang',
            'badge_label' => 'Sudah Daftar Ulang',
        ]);
        $this->assertSame('Nyicil', Cache::get('daftar_ulang_pembayaran_'.$noReg));

        // Cleanup
        AdminActivityLog::where('no_registrasi', $noReg)->delete();
        Cache::forget('daftar_ulang_status_'.$noReg);
        Cache::forget('daftar_ulang_date_'.$noReg);
        Cache::forget('daftar_ulang_pembayaran_'.$noReg);
        $pendaftaran->delete();
        $calonSiswa->delete();
        $studentUser->delete();
        $admin->delete();
    }

    public function test_dedicated_admin_activity_log_page_can_be_accessed(): void
    {
        $admin = User::create([
            'email' => 'admin_page_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.log-aktivitas'));

        $response->assertOk();
        $response->assertSee('Log Aktivitas');
        $response->assertSee('section-log-aktivitas');
        $response->assertSee('Live Realtime');
        $response->assertSee('log-relative-time');
        $response->assertSee('data-timestamp=');
        $response->assertSee('log-filter-menu');
        $response->assertSee('Reset Filter');
        $response->assertSee('log-page-indicator');
        $response->assertSee('Verifikasi Berkas');
        $response->assertSee('Daftar Ulang');
        $response->assertSee('log-select-all', false);
        $response->assertSee('id="log-delete-selected"', false);
        $response->assertSee('Hapus Terpilih');
        $response->assertSee('Admin / Pelaksana');

        $admin->delete();
    }

    public function test_admin_can_delete_an_activity_log(): void
    {
        $admin = User::create([
            'email' => 'admin_delete_log_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
        $log = AdminActivityLog::record(
            actionType: 'verifikasi',
            title: 'Log yang diuji',
            description: 'Pengujian proteksi audit log.',
            noRegistrasi: '001'
        );

        $response = $this->actingAs($admin)->deleteJson(route('admin.log-aktivitas.destroy', $log));

        $response->assertOk()->assertJson([
            'success' => true,
        ]);
        $this->assertDatabaseMissing('admin_activity_logs', ['id' => $log->id]);

        $admin->delete();
    }

    public function test_admin_can_bulk_delete_activity_logs(): void
    {
        $admin = User::create([
            'email' => 'admin_delete_logs_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
        $logs = collect([
            AdminActivityLog::record(actionType: 'verifikasi', title: 'Log terpilih 1'),
            AdminActivityLog::record(actionType: 'daftar_ulang', title: 'Log terpilih 2'),
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.log-aktivitas.destroy-selected'), [
            'ids' => $logs->pluck('id')->all(),
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
        ]);
        foreach ($logs as $log) {
            $this->assertDatabaseMissing('admin_activity_logs', ['id' => $log->id]);
        }

        $admin->delete();
    }

    public function test_admin_can_poll_new_activity_logs(): void
    {
        $admin = User::create([
            'email' => 'admin_poll_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $log = AdminActivityLog::record(
            actionType: 'verifikasi',
            title: 'Verifikasi Berkas Baru',
            description: 'Uji coba polling log otomatis.',
            noRegistrasi: 'POLL-01',
            badgeLabel: 'Terverifikasi',
            badgeColor: 'emerald',
            icon: 'check-circle',
            adminName: 'Admin SPMB'
        );

        $response = $this->actingAs($admin)->getJson(route('admin.log-aktivitas.poll', ['after_id' => $log->id - 1]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'id' => $log->id,
            'title' => 'Verifikasi Berkas Baru',
            'no_registrasi' => 'POLL-01',
        ]);

        $log->delete();
        $admin->delete();
    }

    public function test_admin_can_destroy_all_activity_logs(): void
    {
        $admin = User::create([
            'email' => 'admin_destroy_all_logs_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        AdminActivityLog::record(actionType: 'verifikasi', title: 'Log uji coba 1');
        AdminActivityLog::record(actionType: 'daftar_ulang', title: 'Log uji coba 2');

        $response = $this->actingAs($admin)->postJson(route('admin.log-aktivitas.destroy-all'));

        $response->assertOk()->assertJson([
            'success' => true,
        ]);

        $this->assertEquals(0, AdminActivityLog::count());

        $admin->delete();
    }
}
