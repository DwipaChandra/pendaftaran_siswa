<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BerandaDonutChartFeatureTest extends TestCase
{
    public function test_beranda_donut_chart_renders_distribution_based_on_applicant_count(): void
    {
        $admin = User::create([
            'email' => 'admin_test_'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.beranda'));

        $response->assertOk();

        // 1. Judul Kartu
        $response->assertSee('Distribusi Peminat Jurusan', false);

        // 2. Teks Tengah Donut Chart
        $totalPendaftar = Pendaftaran::count();
        $response->assertSee('id="donut-center-total"', false);
        $response->assertSee((string) $totalPendaftar, false);
        $response->assertSee('TOTAL PENDAFTAR', false);
        $response->assertSee('5 Jurusan', false);

        // 3. Grid Legend Jurusan & Format TKC
        $response->assertSee('TKC', false);
        $response->assertSee('(Tata Kecantikan)', false);
        $response->assertSee('data-legend-code="TKC"', false);

        // 4. Insight Box Berdasarkan Peminat
        $response->assertSee('id="donut-insight-box"', false);
        $response->assertSee('Jurusan paling diminati saat ini', false);

        $admin->delete();
    }
}
