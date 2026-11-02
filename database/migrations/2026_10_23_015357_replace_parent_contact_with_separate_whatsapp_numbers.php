<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('calon_siswa', 'no_wa_ayah')
            || ! Schema::hasColumn('calon_siswa', 'no_wa_ibu')) {
            Schema::table('calon_siswa', function (Blueprint $table): void {
                if (! Schema::hasColumn('calon_siswa', 'no_wa_ayah')) {
                    $table->string('no_wa_ayah', 20)->nullable();
                }

                if (! Schema::hasColumn('calon_siswa', 'no_wa_ibu')) {
                    $table->string('no_wa_ibu', 20)->nullable();
                }
            });
        }

        if (Schema::hasColumn('calon_siswa', 'no_kontak_ortu')) {
            DB::table('calon_siswa')
                ->whereNull('no_wa_ayah')
                ->whereNotNull('no_kontak_ortu')
                ->update(['no_wa_ayah' => DB::raw('no_kontak_ortu')]);

            Schema::table('calon_siswa', function (Blueprint $table): void {
                $table->dropColumn('no_kontak_ortu');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('calon_siswa', 'no_kontak_ortu')) {
            Schema::table('calon_siswa', function (Blueprint $table): void {
                $table->string('no_kontak_ortu', 20)->nullable();
            });
        }

        DB::table('calon_siswa')
            ->whereNull('no_kontak_ortu')
            ->whereNotNull('no_wa_ayah')
            ->update(['no_kontak_ortu' => DB::raw('no_wa_ayah')]);

    }
};
