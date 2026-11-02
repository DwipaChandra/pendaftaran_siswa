<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standardize registration numbers to 3 digits (e.g. '28' -> '028', '29' -> '029')
        Schema::disableForeignKeyConstraints();

        try {
            $pendaftarans = DB::table('pendaftaran')->get(['no_registrasi']);
            foreach ($pendaftarans as $p) {
                $oldReg = (string) $p->no_registrasi;
                if (ctype_digit($oldReg) && strlen($oldReg) < 3) {
                    $newReg = str_pad($oldReg, 3, '0', STR_PAD_LEFT);

                    DB::table('pendaftaran')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                    DB::table('berkas_persyaratan')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                    DB::table('verifikasi_panitia')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                    DB::table('keuangan_siswa')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                    DB::table('pembayaran_siswa')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                    DB::table('admin_activity_logs')->where('no_registrasi', $oldReg)->update(['no_registrasi' => $newReg]);
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive reverse
    }
};
