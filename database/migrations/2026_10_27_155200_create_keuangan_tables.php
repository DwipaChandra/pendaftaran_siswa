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
        Schema::create('keuangan_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi', 10)->collation('utf8mb4_0900_ai_ci')->unique();
            $table->decimal('diskon_spp_custom', 5, 2)->nullable();
            $table->decimal('diskon_dpp_custom', 5, 2)->nullable();
            $table->text('catatan_diskon')->nullable();
            $table->string('status_daftar_ulang', 50)->default('Belum Daftar Ulang');
            $table->string('status_baju', 50)->default('Belum Lunas');
            $table->boolean('is_refunded')->default(false);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->dateTime('refund_date')->nullable();
            $table->text('refund_notes')->nullable();
            $table->timestamps();

            $table->foreign('no_registrasi')
                ->references('no_registrasi')
                ->on('pendaftaran')
                ->onDelete('cascade');
        });

        Schema::create('pembayaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('no_kuitansi', 50)->unique();
            $table->string('no_registrasi', 10)->collation('utf8mb4_0900_ai_ci')->index();
            $table->string('tipe_transaksi', 20)->default('pembayaran'); // 'pembayaran' or 'refund'
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->string('metode_pembayaran', 50)->default('Tunai');
            $table->date('tgl_bayar');
            $table->text('catatan')->nullable();
            $table->string('nama_petugas', 100)->nullable();
            $table->timestamps();

            $table->foreign('no_registrasi')
                ->references('no_registrasi')
                ->on('pendaftaran')
                ->onDelete('cascade');
        });

        Schema::create('pembayaran_siswa_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')
                ->constrained('pembayaran_siswa')
                ->onDelete('cascade');
            $table->string('kategori', 50); // pendaftaran, mos, asuransi, seragam, dpp, spp, osis
            $table->decimal('nominal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_siswa_item');
        Schema::dropIfExists('pembayaran_siswa');
        Schema::dropIfExists('keuangan_siswa');
    }
};
