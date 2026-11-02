<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SimulasiBiayaLandingFeatureTest extends TestCase
{
    public function test_landing_page_renders_interactive_spmb_cost_calculator_section(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('id="section-simulasi-biaya"', false);
        $response->assertSee('href="#section-simulasi-biaya"', false);
        $response->assertSee('Simulasi Biaya Pendaftaran');

        // Split-Screen SMK TI Theme (Slate/Blue/Navy/Sky) & compact step controls
        $response->assertSee('max-w-5xl', false);
        $response->assertSee('bg-slate-50/85', false);
        $response->assertSee('bg-blue-600', false);
        $response->assertSee('to-[#1B2559]', false);
        $response->assertSee('text-sky-300', false);
        $response->assertSee('01 / 04');
        $response->assertSee('>Jalur</span>', false);
        $response->assertSee('>Jurusan</span>', false);
        $response->assertSee('>Gelombang</span>', false);
        $response->assertSee('>Voucher</span>', false);
        $response->assertSee('text-xs sm:text-sm', false);
        $response->assertSee('Kartu Simulasi Calon Siswa');
        $response->assertSee('Belum dipilih');

        // Result view elements & mandatory disclaimers
        $response->assertSee('id="spmb-result-view"', false);
        $response->assertSee('Total Biaya Daftar Ulang');
        $response->assertSee('SPP + OSIS');
        $response->assertSee('Mulai dari Awal');
        $response->assertSee('Rincian biaya di atas tidak termasuk biaya buku, SAS (Sumatif Akhir Semester), PKL, Kunjungan Industri, Ujian Akhir, dan Pelepasan.');
        $response->assertSee('Aturan Pengembalian Biaya (Refund)');
        $response->assertSee('Bila diterima di sekolah negeri, biaya dikembalikan');
        $response->assertSee('kecuali biaya pendaftaran &amp; seragam', false);
    }

    public function test_calculator_contains_complete_2027_2028_pricing_and_discount_rules(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        // Base pricing groups (TI & Tata Kecantikan)
        $response->assertSee('biayaTetap: 2550000', false);
        $response->assertSee('totalAwalNormal: 4000000', false);
        $response->assertSee('totalAwalNormal: 3650000', false);

        // Jalur, Quotas, MOU Timing & Sub-categories
        $response->assertSee('Jalur Umum');
        $response->assertSee('Jalur Prestasi (Akademik & Non-Akademik)', false);
        $response->assertSee('Juara 1, 2, 3 Kelas (Sekolah) atau Tingkat Desa/Kabupaten');
        $response->assertSee('Tingkat Provinsi atau Nasional');
        $response->assertSee('Tingkat Internasional');
        $response->assertSee('Jalur Afirmasi (Keluarga Kurang Mampu)');
        $response->assertSee('Kuota 25');
        $response->assertSee('Jalur KIP (Kartu Indonesia Pintar)');
        $response->assertSee('Kuota 50');
        $response->assertSee('Warga Desa / SMP / MTs MOU');
        $response->assertSee('Tepat Waktu (SPP 30%)');
        $response->assertSee('Lewat Periode (SPP 10%)');
        $response->assertSee('Saudara Kembar');
        $response->assertSee('Memiliki Saudara Kandung yang Sedang Bersekolah di SMK TI Bali Global Badung');
        $response->assertSee('Anak Kandung Guru/Staf SMK TI Bali Global Badung & STIKOM Grup', false);
        $response->assertSee('Kerabat Guru/Staf SMK TI Bali Global Badung & STIKOM Grup', false);
        $response->assertSee('Panti Asuhan MOU');
        $response->assertSee('Putra/Putri Ojek Online');
        $response->assertSee('Jalur Minat & Bakat (Seni/Olahraga)', false);
        $response->assertSee('Jalur Inklusi (Berkebutuhan Khusus/Cacat Fisik)');

        // Jurusan, Gelombang & Voucher options
        $response->assertSee('TJKT (Teknik Jaringan Komputer & Telekomunikasi)', false);
        $response->assertSee('PPLG (Pengembangan Perangkat Lunak & Gim)', false);
        $response->assertSee('DKV (Desain Komunikasi Visual)');
        $response->assertSee('BD (Bisnis Digital)');
        $response->assertSee('Tata Kecantikan');
        $response->assertSee('07 September 2026 – 01 Januari 2027');
        $response->assertSee('02 Januari 2027 – 11 Mei 2027');
        $response->assertSee('12 Mei 2027 – 19 Juli 2027');
        $response->assertSee('Voucher Rekomendasi Siswa Aktif / Alumni');
        $response->assertSee('Voucher HUT SMK TI Bali Global Badung');
    }

    public function test_calculator_remains_accessible_when_registration_wave_is_closed(): void
    {
        $previousWave = Cache::get('ppdb_active_wave');

        try {
            Cache::forever('ppdb_active_wave', 'Tutup Gelombang');

            $response = $this->get(route('home'));

            $response->assertOk();
            $response->assertSee('id="section-simulasi-biaya"', false);
            $response->assertSee('Total Biaya Daftar Ulang');
        } finally {
            if ($previousWave === null) {
                Cache::forget('ppdb_active_wave');
            } else {
                Cache::forever('ppdb_active_wave', $previousWave);
            }
        }
    }
}
