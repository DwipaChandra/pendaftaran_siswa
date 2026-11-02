<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminThemeSettingsTest extends TestCase
{
    /**
     * Test admin dashboard renders the appearance settings trigger button at sidebar bottom.
     */
    public function test_admin_sidebar_renders_settings_trigger_button(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smktibadung.sch.id'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="admin-settings-trigger-btn"', false);
        $response->assertSee('id="admin-settings-btn-status"', false);
        $response->assertSee('id="admin-settings-mode-text"', false);
    }

    /**
     * Test admin dashboard includes the appearance modal popup with light/dark, presets, and density, without custom color pickers.
     */
    public function test_admin_dashboard_includes_appearance_settings_modal(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smktibadung.sch.id'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="admin-theme-modal"', false);
        $response->assertSee('id="theme-mode-light-btn"', false);
        $response->assertSee('id="theme-mode-dark-btn"', false);
        $response->assertSee("darkMode: 'class'", false);
        $response->assertSee('--admin-surface-muted: #182332', false);
        $response->assertSee("html.classList.add('admin-dark', 'dark')", false);
        $response->assertSee("html.classList.remove('admin-dark', 'dark')", false);
        $response->assertSee('data-kpi-tone="blue"', false);
        $response->assertSee('data-kpi-tone="amber"', false);
        $response->assertSee('data-kpi-tone="sky"', false);
        $response->assertSee('data-kpi-tone="orange"', false);
        $response->assertSee('data-kpi-tone="rose"', false);
        $response->assertSee('data-kpi-tone="emerald"', false);
        $response->assertSee('html.admin-dark .kpi-card-front', false);
        $response->assertSee('background-image: none !important;', false);
        $response->assertSee('id="theme-presets-container"', false);
        $response->assertSee('data-preset="default"', false);
        $response->assertSee('data-preset="ocean"', false);
        $response->assertSee('data-preset="emerald"', false);
        $response->assertSee('data-preset="amethyst"', false);
        $response->assertSee('data-preset="crimson"', false);
        $response->assertSee('data-preset="charcoal"', false);
        $response->assertSee('id="theme-density-comfortable-btn"', false);
        $response->assertSee('id="theme-density-compact-btn"', false);
        $response->assertSee('KARTU TANDA PESERTA SELEKSI SPMB', false);
        $response->assertSee('id="admin-print-card-body-grid"', false);
        $response->assertSee('id="admin-print-photo-box"', false);
        $response->assertSee('position: absolute !important;', false);
        $response->assertSee('top: 0 !important;', false);
        $response->assertSee('Yayasan Widya Dharma Shanti', false);
        $response->assertSee('Email Siswa', false);
        $response->assertDontSee('id="custom-sidebar-color-input"', false);
        $response->assertDontSee('id="custom-bg-color-input"', false);
        $response->assertDontSee('Kustom Warna Sendiri (Manual)');
    }

    /**
     * Test admin navbar renders realtime clock and plain work/break status text correctly for work hours (07:05 - 18:00) and break hours.
     */
    public function test_admin_navbar_renders_work_and_break_status_badge(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smktibadung.sch.id'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 1. Saat jam kerja (misal 10:00 WITA)
        Carbon::setTestNow(Carbon::parse('2026-10-22 10:00:00', 'Asia/Makassar'));
        $responseWork = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseWork->assertStatus(200);
        $responseWork->assertSee('id="nav-work-status-text"', false);
        $responseWork->assertSee('Work');
        $responseWork->assertSee('status-work');
        $responseWork->assertDontSee('id="nav-work-status-badge"', false);

        // 2. Tepat awal jam kerja (07:05 WITA)
        Carbon::setTestNow(Carbon::parse('2026-10-22 07:05:00', 'Asia/Makassar'));
        $responseExactStart = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseExactStart->assertSee('Work');

        // 3. Tepat batas akhir jam kerja (18:00 WITA)
        Carbon::setTestNow(Carbon::parse('2026-10-22 18:00:00', 'Asia/Makassar'));
        $responseExactEnd = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseExactEnd->assertSee('Work');

        // 4. Di luar jam kerja (misal 20:30 WITA) -> Break
        Carbon::setTestNow(Carbon::parse('2026-10-22 20:30:00', 'Asia/Makassar'));
        $responseRest = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseRest->assertStatus(200);
        $responseRest->assertSee('Break');
        $responseRest->assertSee('status-break');

        // 5. Sebelum jam kerja (07:04 WITA) -> Break
        Carbon::setTestNow(Carbon::parse('2026-10-22 07:04:00', 'Asia/Makassar'));
        $responseEarly = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseEarly->assertSee('Break');

        Carbon::setTestNow();
    }
}
