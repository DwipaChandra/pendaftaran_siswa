<?php

namespace App\Console\Commands;

use App\Models\AdminActivityLog;
use App\Models\BerkasPersyaratan;
use App\Models\CalonSiswa;
use App\Models\KeuanganSiswa;
use App\Models\PembayaranSiswa;
use App\Models\PembayaranSiswaItem;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use App\Services\KeuanganService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDummyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spmb:clean-dummy {--force : Jalankan pembersihan tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data dummy/pengujian dan rapikan log audit serta standarisasi nomor registrasi';

    /**
     * Execute the console command.
     */
    public function handle(KeuanganService $keuanganService): int
    {
        $this->info('Memulai pembersihan data pengujian SPMB...');

        // 1. Bersihkan raw emails di AdminActivityLog
        $logs = AdminActivityLog::with('pendaftaran.calonSiswa')
            ->where('description', 'like', '%@%')
            ->get();

        $cleanedLogCount = 0;
        foreach ($logs as $log) {
            $cleanName = $log->pendaftaran?->calonSiswa?->nama_lengkap;
            if (! $cleanName || str_contains($cleanName, '@')) {
                $cleanName = 'Calon Siswa #'.str_pad((string) ($log->no_registrasi ?: '001'), 3, '0', STR_PAD_LEFT);
            }

            $desc = preg_replace('/[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9_\.\-]+\.[a-zA-Z]{2,}/', $cleanName, $log->description);
            if ($desc !== $log->description) {
                $log->update(['description' => $desc]);
                $cleanedLogCount++;
            }
        }
        $this->info("Berhasil merapikan {$cleanedLogCount} deskripsi log aktivitas.");

        // 2. Standarisasi padding no_registrasi di seluruh tabel
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

        // 3. Hapus data dummy siswa yang tidak valid (dfsf, apa aja terserah, Tester...)
        $dummyKeywords = ['dfsf', 'apa aja terserah', 'Tester Siswa Keuangan', 'lkjdklsdf'];
        $dummyStudents = CalonSiswa::where(function ($q) use ($dummyKeywords) {
            foreach ($dummyKeywords as $kw) {
                $q->orWhere('nama_lengkap', 'like', "%{$kw}%");
            }
        })->get();

        $deletedDummyCount = 0;
        foreach ($dummyStudents as $cs) {
            $pendaftaran = Pendaftaran::where('id_siswa', $cs->id_siswa)->first();
            if ($pendaftaran) {
                $reg = $pendaftaran->no_registrasi;
                PembayaranSiswaItem::whereIn('pembayaran_id', function ($query) use ($reg) {
                    $query->select('id')->from('pembayaran_siswa')->where('no_registrasi', $reg);
                })->delete();
                PembayaranSiswa::where('no_registrasi', $reg)->delete();
                KeuanganSiswa::where('no_registrasi', $reg)->delete();
                BerkasPersyaratan::where('no_registrasi', $reg)->delete();
                VerifikasiPanitia::where('no_registrasi', $reg)->delete();
                $pendaftaran->delete();
                Cache::forget('daftar_ulang_status_'.$reg);
                Cache::forget('daftar_ulang_date_'.$reg);
                Cache::forget('daftar_ulang_pembayaran_'.$reg);
            }
            $userId = $cs->id_user;
            $cs->delete();
            if ($userId) {
                User::where('id_user', $userId)->where('role', 'siswa')->delete();
            }
            $deletedDummyCount++;
        }
        $this->info("Berhasil menghapus {$deletedDummyCount} profil siswa dummy.");

        // 4. Bersihkan orphan test users & calon siswa (e.g. 'Calon Siswa Pengujian', 'Siswa Tes Delete' without pendaftaran)
        $orphanCalonSiswa = CalonSiswa::whereDoesntHave('pendaftaran')
            ->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%Pengujian%')
                    ->orWhere('nama_lengkap', 'like', '%Tes Delete%')
                    ->orWhere('nama_lengkap', 'like', '%Tester%')
                    ->orWhere('nama_lengkap', 'like', '%Siswa Filter%')
                    ->orWhere('nisn', '1234567890')
                    ->orWhereNull('asal_sekolah');
            })->get();

        foreach ($orphanCalonSiswa as $orphan) {
            $uId = $orphan->id_user;
            $orphan->delete();
            if ($uId) {
                User::where('id_user', $uId)->where('role', 'siswa')->delete();
            }
        }

        // 5. Normalisasi angka transaksi aneh / desimal ganjil pada data testing (seperti 469.998 pada 013)
        $oddItems = PembayaranSiswaItem::where('nominal', 19998)->get();
        foreach ($oddItems as $item) {
            $item->update(['nominal' => 20000]);
        }
        $oddPayments = PembayaranSiswa::where('total_bayar', 469998)->get();
        foreach ($oddPayments as $pay) {
            $pay->update(['total_bayar' => 470000]);
        }

        // 6. Sinkronkan ulang seluruh status keuangan
        $allPendaftarans = Pendaftaran::with(['keuangan', 'pembayaran.items'])->get();
        foreach ($allPendaftarans as $p) {
            $keuanganService->syncKeuanganStatus($p);
        }

        $this->info('Sinkronisasi dan pembersihan selesai dengan sukses.');

        return Command::SUCCESS;
    }
}
