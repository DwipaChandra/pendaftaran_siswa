<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->string('sub_jalur', 100)->nullable()->after('id_jalur');
            $table->string('nama_sub_jalur', 255)->nullable()->after('sub_jalur');
            $table->string('voucher_jenis', 50)->nullable()->after('nama_sub_jalur');
            $table->string('voucher_kode', 100)->nullable()->after('voucher_jenis');
            $table->boolean('mou_tepat_waktu')->default(true)->after('voucher_kode');
        });

        Schema::table('keuangan_siswa', function (Blueprint $table) {
            $table->string('durasi_spp', 50)->nullable()->after('diskon_spp_custom');
            $table->string('durasi_dpp', 50)->nullable()->after('diskon_dpp_custom');
            $table->decimal('diskon_jalur_persen', 5, 2)->nullable()->after('durasi_dpp');
            $table->decimal('diskon_jurusan_persen', 5, 2)->nullable()->after('diskon_jalur_persen');
            $table->decimal('diskon_pendaftaran_persen', 5, 2)->nullable()->after('diskon_jurusan_persen');
            $table->decimal('minimal_daftar_ulang', 12, 2)->nullable()->after('diskon_pendaftaran_persen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan_siswa', function (Blueprint $table) {
            $table->dropColumn([
                'durasi_spp',
                'durasi_dpp',
                'diskon_jalur_persen',
                'diskon_jurusan_persen',
                'diskon_pendaftaran_persen',
                'minimal_daftar_ulang',
            ]);
        });

        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropColumn([
                'sub_jalur',
                'nama_sub_jalur',
                'voucher_jenis',
                'voucher_kode',
                'mou_tepat_waktu',
            ]);
        });
    }
};
