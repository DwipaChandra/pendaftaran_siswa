<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentDropdownFeatureTest extends TestCase
{
    public function test_authenticated_student_sees_updated_dropdown_structure_and_blue_accent(): void
    {
        $user = User::create([
            'email' => 'buditest_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Budi Pratama',
            'no_whatsapp' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Budi Pratama');
        $response->assertSee('Siswa');
        $response->assertSee('Formulir Pendaftaran');
        $response->assertSee('Ganti kata sandi');
        $response->assertSee(route('pendaftaran.password.edit'));
        $response->assertSee('Keluar');
        $response->assertSee('bg-blue-600', false);
        $response->assertSee('text-blue-600', false);
        $response->assertSee('user-dropdown-menu', false);
        $response->assertSee('global-logout-modal', false);
        $response->assertSee('Yakin ingin keluar?');

        $user->calonSiswa()->delete();
        $user->delete();
    }

    public function test_student_password_page_can_be_rendered(): void
    {
        $user = User::create([
            'email' => 'pagetest_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Halaman Siswa',
            'no_whatsapp' => '081234567899',
        ]);

        $response = $this->actingAs($user)->get(route('pendaftaran.password.edit'));

        $response->assertStatus(200);
        $response->assertSee('Ganti Kata Sandi');
        $response->assertSee('PASSWORD BARU');
        $response->assertSee('KONFIRMASI PASSWORD');
        $response->assertSee('Simpan Kata Sandi Baru');
        $response->assertSee(route('home'));
        $response->assertSee(route('pendaftaran.password.update'));

        $user->calonSiswa()->delete();
        $user->delete();
    }

    public function test_student_can_change_password_successfully(): void
    {
        $user = User::create([
            'email' => 'pwtest_'.uniqid().'@example.com',
            'password' => Hash::make('oldpassword123'),
            'role' => 'siswa',
        ]);

        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Siswa Test',
            'no_whatsapp' => '081234567891',
        ]);

        $response = $this->actingAs($user)->put(route('pendaftaran.password.update'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('pendaftaran.password.edit'));
        $response->assertSessionHas('success', 'Kata sandi berhasil diperbarui.');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        $user->calonSiswa()->delete();
        $user->delete();
    }

    public function test_student_password_change_requires_confirmation_and_min_length(): void
    {
        $user = User::create([
            'email' => 'valtest_'.uniqid().'@example.com',
            'password' => Hash::make('oldpassword123'),
            'role' => 'siswa',
        ]);

        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Siswa Validasi',
            'no_whatsapp' => '081234567892',
        ]);

        // Short password
        $responseShort = $this->actingAs($user)->put(route('pendaftaran.password.update'), [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $responseShort->assertSessionHasErrors(['password']);

        // Mismatched confirmation
        $responseMismatch = $this->actingAs($user)->put(route('pendaftaran.password.update'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'different123',
        ]);
        $responseMismatch->assertSessionHasErrors(['password']);

        $user->calonSiswa()->delete();
        $user->delete();
    }

    public function test_admin_navbar_renders_account_pill_and_dropdown(): void
    {
        $admin = User::create([
            'email' => 'admintest_'.uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('admin-user-menu-container', false);
        $response->assertSee('admin-nav-user-btn', false);
        $response->assertSee('admin-user-dropdown-menu', false);
        $response->assertSee('nav-realtime-date', false);
        $response->assertSee('nav-realtime-time', false);
        $response->assertSee('WITA');
        $response->assertSee('Administrator');
        $response->assertSee($admin->email);
        $response->assertDontSee('Buka Landing Page');
        $response->assertSee(route('admin.password.edit'));
        $response->assertSee('Ganti kata sandi');
        $response->assertSee('Keluar');
        $response->assertSee('handleAdminLogout(event)', false);

        $admin->delete();
    }
}
