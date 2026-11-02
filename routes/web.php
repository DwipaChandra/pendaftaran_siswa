<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;

// Public Landing & Info Pages (Protected from Admin: Auto-redirect to Admin Dashboard)
Route::middleware(['redirect.admin'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/syarat-ketentuan', [HomeController::class, 'termsAndConditions'])->name('syarat-ketentuan');
    Route::get('/kebijakan-privasi', [HomeController::class, 'privacyPolicy'])->name('kebijakan-privasi');

    // Auth Views
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::get('/sign', [AuthController::class, 'showRegister']); // Alias
});

Route::get('/status-gelombang', [HomeController::class, 'waveStatus'])->name('home.wave-status');
Route::get('/status-kuota', [HomeController::class, 'quotaStatus'])->name('home.quota-status');
Route::get('/unduh-panduan', [HomeController::class, 'downloadPanduan'])->name('panduan.download');
Route::get('/download-panduan', [HomeController::class, 'downloadPanduan']); // Alias

// Auth Actions: Login, Register, Logout
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // Fallback GET logout

// Calon Siswa (Protected: Role Siswa)
Route::middleware(['role.siswa'])->prefix('pendaftaran')->name('pendaftaran.')->group(function () {
    Route::get('/langkah-1', [PendaftaranController::class, 'step1'])->name('step1');
    Route::post('/langkah-1', [PendaftaranController::class, 'postStep1'])->name('step1.post');

    Route::get('/langkah-2', [PendaftaranController::class, 'step2'])->name('step2');
    Route::post('/langkah-2', [PendaftaranController::class, 'postStep2'])->name('step2.post');

    Route::get('/langkah-3', [PendaftaranController::class, 'step3'])->name('step3');
    Route::post('/langkah-3', [PendaftaranController::class, 'postStep3'])->name('step3.post');

    Route::get('/langkah-4', [PendaftaranController::class, 'step4'])->name('step4');
    Route::post('/selesai', [PendaftaranController::class, 'submitRegistration'])->name('submit');

    Route::get('/kartu', [PendaftaranController::class, 'card'])->name('card');
    Route::post('/revisi-berkas', [PendaftaranController::class, 'reuploadDocuments'])->name('revisi-berkas');
    Route::get('/ganti-password', [AuthController::class, 'showStudentPasswordForm'])->name('password.edit');
    Route::put('/ganti-password', [AuthController::class, 'updateStudentPassword'])->name('password.update');
});

// Admin / Panitia (Protected: Role Admin & Panitia)
Route::middleware(['role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/beranda', [AdminController::class, 'dashboard'])->name('beranda');
    Route::get('/status-berkas', [AdminController::class, 'dashboard'])->name('status-berkas');
    Route::get('/kuota-rombel', [AdminController::class, 'dashboard'])->name('kuota-rombel');
    Route::get('/tabel-jurusan', [AdminController::class, 'dashboard'])->name('tabel-jurusan');
    Route::get('/daftar-ulang', [AdminController::class, 'dashboard'])->name('daftar-ulang');
    Route::post('/daftar-ulang/status', [AdminController::class, 'updateDaftarUlangStatus'])->name('daftar-ulang.status');
    Route::get('/keuangan', [AdminController::class, 'dashboard'])->name('keuangan');
    Route::post('/keuangan/bayar', [KeuanganController::class, 'recordPayment'])->name('keuangan.bayar');
    Route::post('/keuangan/void-kuitansi', [KeuanganController::class, 'voidKuitansi'])->name('keuangan.void-kuitansi');
    Route::post('/keuangan/reset-pembayaran', [KeuanganController::class, 'resetPayment'])->name('keuangan.reset-pembayaran');
    Route::post('/keuangan/hapus-siswa', [KeuanganController::class, 'deleteStudent'])->name('keuangan.hapus-siswa');
    Route::post('/keuangan/reset-terpilih', [KeuanganController::class, 'resetSelected'])->name('keuangan.reset-terpilih');
    Route::post('/keuangan/hapus-terpilih', [KeuanganController::class, 'destroySelected'])->name('keuangan.destroy-selected');
    Route::post('/keuangan/refund', [KeuanganController::class, 'processRefund'])->name('keuangan.refund');
    Route::post('/keuangan/diskon-spp', [KeuanganController::class, 'updateCustomSppDiscount'])->name('keuangan.diskon-spp');
    Route::get('/keuangan/kuitansi/{no_kuitansi}', [KeuanganController::class, 'getKuitansiDetail'])->name('keuangan.kuitansi');
    Route::get('/keuangan/detail/{no_registrasi}', [KeuanganController::class, 'getStudentFinanceDetail'])->name('keuangan.detail');
    Route::post('/testing/cleanup-dummy', [AdminController::class, 'cleanupDummyData'])->name('testing.cleanup-dummy');
    Route::get('/office', [AdminController::class, 'dashboard'])->name('office');
    Route::get('/log-aktivitas', [AdminController::class, 'dashboard'])->name('log-aktivitas');
    Route::get('/log-aktivitas/poll', [AdminController::class, 'pollActivityLogs'])->name('log-aktivitas.poll');
    Route::delete('/log-aktivitas/{log}', [AdminController::class, 'destroyActivityLog'])->name('log-aktivitas.destroy');
    Route::post('/log-aktivitas/hapus-terpilih', [AdminController::class, 'destroySelectedActivityLogs'])->name('log-aktivitas.destroy-selected');
    Route::post('/log-aktivitas/hapus-semua', [AdminController::class, 'destroyAllActivityLogs'])->name('log-aktivitas.destroy-all');
    Route::get('/ganti-password', [AuthController::class, 'showAdminPasswordForm'])->name('password.edit');
    Route::put('/ganti-password', [AuthController::class, 'updateAdminPassword'])->name('password.update');
    Route::post('/verifikasi/{no_registrasi}', [AdminController::class, 'verifikasi'])->name('verifikasi');
    Route::get('/pendaftaran/poll', [AdminController::class, 'pollApplicants'])->name('pendaftaran.poll');
    Route::post('/pendaftaran/{no_registrasi}/data', [AdminController::class, 'updateApplicantData'])->name('pendaftaran.data.update');
    Route::post('/pendaftaran/{no_registrasi}/berkas', [AdminController::class, 'updateDocuments'])->name('berkas.update');
    Route::post('/undo', [AdminController::class, 'undoChange'])->name('undo');
    Route::delete('/pendaftaran/{no_registrasi}', [AdminController::class, 'destroy'])->name('pendaftaran.destroy');
    Route::post('/pendaftaran/hapus-terpilih', [AdminController::class, 'destroySelected'])->name('pendaftaran.destroy-selected');
    Route::get('/kartu/{no_registrasi}', [AdminController::class, 'card'])->name('kartu');
    Route::post('/gelombang', [AdminController::class, 'setWave'])->name('setWave');
    Route::post('/jurusan/{jurusan}/kuota', [AdminController::class, 'updateJurusanQuota'])->name('jurusan.kuota.update');
});

// File / Storage serving fallback (ensures berkas and photos can always be accessed reliably)
Route::get('/storage/berkas/{filename}', function (string $filename) {
    $cleanFilename = basename(rawurldecode($filename));
    $path = storage_path('app/public/berkas/'.$cleanFilename);

    if (! file_exists($path) || is_dir($path)) {
        abort(404);
    }

    $mime = mime_content_type($path) ?: 'application/octet-stream';

    return response()->file($path, [
        'Content-Type' => $mime,
        'Cache-Control' => 'no-cache, private',
    ]);
})->where('filename', '.*')->name('storage.berkas');

Route::get('/storage/{path}', function (string $path) {
    $cleanPath = ltrim(rawurldecode($path), '/\\');
    $fullPath = storage_path('app/public/'.$cleanPath);
    $basePath = realpath(storage_path('app/public'));
    $realPath = realpath($fullPath);

    if (! $realPath || ! $basePath || ! str_starts_with($realPath, $basePath) || is_dir($realPath)) {
        abort(404);
    }

    $mime = mime_content_type($realPath) ?: 'application/octet-stream';

    return response()->file($realPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'no-cache, private',
    ]);
})->where('path', '.*')->name('storage.file');
