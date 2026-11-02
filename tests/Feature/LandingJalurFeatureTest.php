<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\JalurPendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class LandingJalurFeatureTest extends TestCase
{
    /**
     * Test landing page displays the 6 registration pathway cards.
     */
    public function test_landing_page_displays_all_six_jalur_cards(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Jalur Pendaftaran');
        $response->assertSee('Jalur 01');
        $response->assertSee('Umum');
        $response->assertSee('Jalur 02');
        $response->assertSee('KIP');
        $response->assertSee('Jalur 03');
        $response->assertSee('Afirmasi');
        $response->assertSee('Jalur 04');
        $response->assertSee('Prestasi');
        $response->assertSee('Jalur 05');
        $response->assertSee('Minat &amp; Bakat', false);
        $response->assertSee('Jalur 06');
        $response->assertSee('Khusus');

        // Verify 3D flip card structure with front and back discount lists
        $response->assertSee('bento-card-front', false);
        $response->assertSee('bento-card-back', false);
        $response->assertSee('bento-back-list', false);
        $response->assertSee('bento-back-check', false);
        $response->assertSee('Potongan SPP');
        $response->assertSee('Potongan DPP');
    }

    /**
     * Test step 2 preselects Jalur Minat Bakat when query parameter is provided.
     */
    public function test_step2_preselects_jalur_minat_bakat(): void
    {
        $jalur = JalurPendaftaran::firstOrCreate(['nama_jalur' => 'Jalur Minat Bakat']);

        $user = User::create([
            'email' => 'jalur_test_'.Str::random(5).'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);

        CalonSiswa::create([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Tester Jalur Minat Bakat',
            'jenis_kelamin' => 'L',
            'nisn' => (string) rand(1000000000, 9999999999),
            'asal_sekolah' => 'SMP Negeri 1',
            'alamat' => 'Jl. Pengujian No. 123',
            'no_telepon' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get(route('pendaftaran.step2', ['jalur' => 'bakat']));
        $response->assertStatus(200);
        $response->assertSee('value="'.$jalur->id_jalur.'" selected', false);

        // Clean up test user
        $user->calonSiswa()->delete();
        $user->delete();
    }
}
