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
        // 1. Tentukan record kanonik TKC (Tata Kecantikan)
        $canonicalTkc = DB::table('jurusan')
            ->where('nama_jurusan', 'like', '%TKC%')
            ->orderBy('id_jurusan')
            ->first();

        if (! $canonicalTkc) {
            $canonicalTkc = DB::table('jurusan')
                ->where('nama_jurusan', 'like', '%Kecantikan%')
                ->orderBy('id_jurusan')
                ->first();
        }

        if (! $canonicalTkc) {
            $canonicalId = DB::table('jurusan')->insertGetId([
                'nama_jurusan' => 'TKC (Tata Kecantikan)',
                'kuota' => 72,
            ]);
        } else {
            $canonicalId = $canonicalTkc->id_jurusan;
            DB::table('jurusan')
                ->where('id_jurusan', $canonicalId)
                ->update(['nama_jurusan' => 'TKC (Tata Kecantikan)']);
        }

        // 2. Cari semua jurusan duplikat yang mengarah ke Tata Kecantikan / TKC selain kanonik
        $duplicateJurusanList = DB::table('jurusan')
            ->where('id_jurusan', '!=', $canonicalId)
            ->where(function ($query) {
                $query->where('nama_jurusan', 'like', '%Kecantikan%')
                    ->orWhere('nama_jurusan', 'like', '%TKC%');
            })
            ->get();

        foreach ($duplicateJurusanList as $dup) {
            // Pindahkan seluruh pendaftaran terkait ke id_jurusan kanonik
            DB::table('pendaftaran')
                ->where('id_jurusan', $dup->id_jurusan)
                ->update(['id_jurusan' => $canonicalId]);

            // Hapus jurusan duplikat dari master data
            DB::table('jurusan')
                ->where('id_jurusan', $dup->id_jurusan)
                ->delete();
        }

        // 3. Pastikan kelima jurusan resmi memiliki nama standar
        $standardJurusans = [
            'PPLG' => 'PPLG (Pengembangan Perangkat Lunak & Gim)',
            'DKV' => 'DKV (Desain Komunikasi Visual)',
            'BD' => 'BD (Bisnis Digital)',
            'TJKT' => 'TJKT (Teknik Jaringan Komputer & Telekomunikasi)',
            'TKC' => 'TKC (Tata Kecantikan)',
        ];

        foreach ($standardJurusans as $code => $fullName) {
            $existing = DB::table('jurusan')
                ->where('nama_jurusan', 'like', $code.'%')
                ->orWhere('nama_jurusan', $fullName)
                ->first();

            if ($existing) {
                DB::table('jurusan')
                    ->where('id_jurusan', $existing->id_jurusan)
                    ->update(['nama_jurusan' => $fullName]);
            }
        }

        // 4. Tambahkan unique constraint pada nama_jurusan agar bug duplikasi tidak terulang
        if (Schema::hasTable('jurusan')) {
            $indexes = DB::select("SHOW INDEXES FROM jurusan WHERE Column_name = 'nama_jurusan'");
            if (empty($indexes)) {
                Schema::table('jurusan', function (Blueprint $table) {
                    $table->unique('nama_jurusan', 'jurusan_nama_jurusan_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jurusan')) {
            Schema::table('jurusan', function (Blueprint $table) {
                $table->dropUnique('jurusan_nama_jurusan_unique');
            });
        }
    }
};
